<?php

namespace App\Livewire\Main;

use App\Helpers\NotificationHelper;
use App\Mail\NewChatLeadNotification;
use App\Mail\VisitorLeadAcknowledgement;
use App\Models\ChatLead;
use App\Models\User;
use App\Services\ChatKnowledgeBase;
use App\Services\LeadSpamFilter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWidget extends Component
{
    public array $messages = [];

    public string $newMessage = '';

    public bool $isOpen = false;

    public bool $isThinking = false;

    public bool $hasUnread = false;

    /** Set to true on the request where we captured a brand-new lead. */
    public bool $leadJustCaptured = false;

    protected int $maxHistory = 20;

    protected string $model = 'gemini-3.6-flash';

    protected int $leadLockMinutes = 30;

    /** Session rate limit: 15 messages per minute. */
    protected int $sessionRateLimit = 15;

    /** IP rate limit: 60 messages per hour. */
    protected int $ipRateLimit = 60;

    protected int $ipRateWindowSeconds = 3600;

    /* ──────────────────────────────────────────────────────────── */
    /*  Quick reply chips                                          */
    /* ──────────────────────────────────────────────────────────── */

    public array $quickReplies = [
        'services' => [
            'label'   => 'See our services',
            'icon'    => 'fal fa-briefcase',
            'message' => 'What services does Polysphere Tech offer?',
        ],
        'projects' => [
            'label'   => 'Show projects',
            'icon'    => 'fal fa-layer-group',
            'message' => 'Can you show me some of your recent projects?',
        ],
        'hiring' => [
            'label'   => 'Are you hiring?',
            'icon'    => 'fal fa-user-plus',
            'message' => 'Are you currently hiring?',
        ],
        'human' => [
            'label'   => 'Talk to a human',
            'icon'    => 'fal fa-user-headset',
            'message' => "I'd like to speak to a human, please.",
        ],
    ];

    public function getShouldShowQuickRepliesProperty(): bool
    {
        foreach ($this->messages as $msg) {
            if (($msg['role'] ?? '') === 'user') {
                return false;
            }
        }

        return ! $this->isThinking;
    }

    public function sendQuickReply(string $key): void
    {
        if ($this->isThinking) {
            return;
        }

        $chip = $this->quickReplies[$key] ?? null;
        if (! $chip) {
            return;
        }

        $this->newMessage = $chip['message'];
        $this->send();
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Human handoff — short-circuit                              */
    /* ──────────────────────────────────────────────────────────── */

    /**
     * Detect messages where the visitor clearly wants a human.
     * These get a canned reply without calling Gemini — so a
     * Gemini hiccup never blocks the most important conversion path.
     */
    protected function isHumanHandoffRequest(string $text): bool
    {
        $normalized = mb_strtolower(trim($text));

        $phrases = [
            'talk to a human',
            'speak to a human',
            'talk to someone',
            'speak to someone',
            'talk to a person',
            'speak to a person',
            'talk to a real person',
            'speak to a real person',
            'talk to an agent',
            'speak to an agent',
            'real person',
            'real human',
            'human please',
            'chat with a human',
            'chat with someone',
            'someone from the team',
            'talk to the team',
            'speak to the team',
            'talk to your team',
            'speak to your team',
            'customer support',
            'customer service',
            'representative',
            'get a callback',
            'request a callback',
            'book a call',
            'schedule a call',
        ];

        foreach ($phrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Canned reply for the human-handoff path. Varies slightly by
     * whether the visitor has already shared an email.
     */
    protected function humanHandoffReply(): string
    {
        $sessionId    = session()->getId();
        $existingLead = ChatLead::where('session_id', $sessionId)->first();

        if ($existingLead && ! empty($existingLead->email)) {
            return "Absolutely — I've already passed your details to the team, and someone will be in touch soon. "
                . "If you'd like to add a phone number or anything else useful, just drop it here and I'll make sure it reaches them.";
        }

        return "Of course — I'll get a real person from our team to reach out. "
            . "What's the best email address for them to use? "
            . "You can also add a phone number if you'd like a call instead.";
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Prompt construction                                        */
    /* ──────────────────────────────────────────────────────────── */

    protected function faqBlock(): string
    {
        return collect(config('faqs.items'))
            ->map(fn($faq) => "Q: {$faq['question']}\nA: {$faq['answer']}")
            ->implode("\n\n");
    }

    protected function systemPrompt(): string
    {
        $faqBlock = $this->faqBlock();
        $kb       = app(ChatKnowledgeBase::class)->build();
        $baseUrl  = rtrim(config('app.url'), '/');

        $leadContext = $this->leadJustCaptured
            ? "\n\n═══════════════════════════\nIMPORTANT — LEAD JUST CAPTURED\n═══════════════════════════\n"
            . "The visitor just shared their email address in their last message. Their contact "
            . "details have been saved and the Polysphere Tech team has been notified. In your "
            . "response:\n"
            . "- Warmly acknowledge that you've got their details.\n"
            . "- Confirm that someone from the team will reach out within 24 hours.\n"
            . "- DO NOT ask them to email contact@polyspheretech.com — the connection is already made.\n"
            . "- If they asked a question in the same message, still answer it briefly.\n"
            . "- You may ALSO naturally ask ONE follow-up question to help the team prepare: "
            .   "either \"What company are you with?\" OR \"Is there a phone number that works best "
            .   "for a callback?\" — pick whichever fits the flow. Do not ask for both. Do not "
            .   "make it feel like a form."
            : '';

        return <<<PROMPT
            You are Sphere, the AI assistant embedded on the Polysphere Tech website.
            You represent the brand — warm, natural, human. Never sound like a script.

            ═══════════════════════════
            COMPANY OVERVIEW
            ═══════════════════════════
            Polysphere Tech is an IT company in Accra, Ghana, specializing in custom
            software development, SaaS platforms, IT consulting, and digital
            transformation for modern businesses.

            Core services:
            - Custom software development (web, mobile, internal tools)
            - SaaS engineering (build & scale end-to-end)
            - Digital transformation (modernizing legacy systems / workflows)
            - IT consulting (strategy, architecture review, tooling advice)

            Contact & canonical URLs (the ONLY general URLs you may share that are
            not in the LIVE KNOWLEDGE BASE below):
            - Homepage: {$baseUrl}
            - Services index: {$baseUrl}/services
            - Projects / portfolio: {$baseUrl}/projects
            - Team index: {$baseUrl}/team
            - Careers index: {$baseUrl}/careers
            - Contact email: contact@polyspheretech.com
            - Careers email: careers@polyspheretech.com
            - Phone: +233 (59) 756-3427
            - Address: Accra, Ghana

            ═══════════════════════════
            OFFICIAL FAQ (primary source of truth)
            ═══════════════════════════
            {$faqBlock}

            ═══════════════════════════
            LIVE KNOWLEDGE BASE
            ═══════════════════════════
            Real, current data pulled from Polysphere Tech's own systems. Treat each
            section as the SINGLE SOURCE OF TRUTH for its topic. If something isn't
            listed, it does not exist (yet).

            {$kb}

            ═══════════════════════════
            HOW TO USE THE LIVE DATA
            ═══════════════════════════

            JOBS / CAREERS / HIRING
            - Consult LIVE JOB VACANCIES for anything job-related.
            - If no positions: say so, invite them to email careers@polyspheretech.com.
            - If positions are open: summarise the 2-4 most relevant. Filter for
              "remote only", "engineering", "Accra" etc. when asked.
            - Never invent salary or closing dates. Quote only what's listed.

            TEAM
            - Only describe people listed in LIVE TEAM.
            - STRICT: use ONLY the fields shown (Name, Role, Dept, Skills, Bio). Do not
              infer seniority, years of experience, education, or achievements.

            PROJECTS
            - Recommend only projects from LIVE PROJECTS. Share their URLs.

            SERVICES
            - LIVE SERVICES lists what Polysphere offers right now.

            ═══════════════════════════
            CONVERSATION STYLE
            ═══════════════════════════
            - Greetings: brief and warm, then invite them to ask.
            - Keep answers to 2-4 sentences unless detail is requested.
            - Plain, confident language — no corporate filler.
            - Ask a clarifying question when the request is vague.

            ═══════════════════════════
            LEAD CAPTURE — HOW TO COLLECT CONTACT DETAILS
            ═══════════════════════════
            Your goal is to help genuine visitors get in touch with the team. Do this
            NATURALLY — never sound like a form or a sales script.

            - Ask for their EMAIL first when they show real interest (asking for a
              quote, a timeline, a call, discussing a specific project).
            - Once you have their email, you may ask ONE additional follow-up
              question — not more — to help the team prepare:
                • "What company are you with?" OR
                • "Is there a phone number that works best for a callback?"
              Pick whichever feels more natural in context. Do NOT ask for both.
              Do NOT ask for these if the visitor hasn't yet shown real interest.
            - If they volunteer a company name or phone number on their own, that's
              great — you don't need to ask.
            - ONE nudge per conversation. If they decline to share, drop it and move on.

            ═══════════════════════════
            HUMAN HANDOFF
            ═══════════════════════════
            If the visitor asks to speak to a human, a person, an agent, or the team
            directly — or if they express frustration that they can't get what they
            need from you — respond warmly and ask for their email so a human can
            follow up. Do NOT keep answering as the bot. Prioritise getting their
            contact details so a real person can take over within 24 hours.

            ═══════════════════════════
            LINKS (STRICT)
            ═══════════════════════════
            - Only share URLs from the canonical list above or the LIVE KNOWLEDGE BASE.
            - Write URLs as plain, full, clickable URLs. No markdown link syntax.

            ═══════════════════════════
            HANDLING SPECIFIC TOPICS
            ═══════════════════════════
            - Pricing: depends on scope, complexity, timeline. Invite them to share
              their email OR contact@polyspheretech.com. Never invent a number.
            - Off-topic requests (weather, trivia, poems, jokes, unrelated coding):
              DO NOT answer. Politely decline and offer what you CAN help with.
            - Frustration / complaints: acknowledge, point them at contact@polyspheretech.com.

            ═══════════════════════════
            HARD RULES
            ═══════════════════════════
            - Never fabricate facts: clients, results, staff, numbers, awards, years
              in business, job openings, team members, projects, or services.
            - Never invent URLs, salaries, dates, or timelines.
            - If you don't know, say so and redirect to the team.
            - Never claim to be human. Never pretend to take real actions.
            - Never produce creative writing on request.
            - Never ask for more than two pieces of contact info in one conversation.
            {$leadContext}
            PROMPT;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Lifecycle                                                  */
    /* ──────────────────────────────────────────────────────────── */

    public function mount(): void
    {
        $this->messages[] = [
            'role'    => 'assistant',
            'content' => "Hi there! 👋 I'm Sphere, Polysphere Tech's assistant. Ask me about our services, process, projects, team, open roles, or how to get started.",
            'time'    => now()->format('g:i A'),
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->hasUnread = false;
        }
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Send                                                       */
    /* ──────────────────────────────────────────────────────────── */

    public function send(): void
    {
        $text = trim($this->newMessage);

        if ($text === '' || $this->isThinking) {
            return;
        }

        // ─── Rate limiting: session (15/min) ───────────────────────
        $sessionKey = 'chat-widget:' . session()->getId();

        if (RateLimiter::tooManyAttempts($sessionKey, $this->sessionRateLimit)) {
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => "You're sending messages a bit fast — give me a few seconds and try again.",
                'time'    => now()->format('g:i A'),
            ];
            return;
        }

        RateLimiter::hit($sessionKey, 60);

        // ─── Rate limiting: IP (60/hour) ───────────────────────────
        $ipKey = 'chat-widget-ip:' . request()->ip();

        if (RateLimiter::tooManyAttempts($ipKey, $this->ipRateLimit)) {
            Log::warning('Chat widget IP rate limit hit', [
                'ip'    => request()->ip(),
                'agent' => request()->userAgent(),
            ]);

            $this->messages[] = [
                'role'    => 'assistant',
                'content' => "We've received a lot of messages from this network. Please try again later, or email contact@polyspheretech.com.",
                'time'    => now()->format('g:i A'),
            ];
            return;
        }

        RateLimiter::hit($ipKey, $this->ipRateWindowSeconds);

        // ─── Human handoff short-circuit ───────────────────────────
        if ($this->isHumanHandoffRequest($text)) {
            $this->messages[] = [
                'role'    => 'user',
                'content' => $text,
                'time'    => now()->format('g:i A'),
            ];

            $this->messages[] = [
                'role'    => 'assistant',
                'content' => $this->humanHandoffReply(),
                'time'    => now()->format('g:i A'),
            ];

            $this->newMessage = '';

            // Also try to capture an email if they included one in the
            // same message ("talk to a human, my email is x@y.com").
            $this->captureLeadIfPresent($text);

            if (! $this->isOpen) {
                $this->hasUnread = true;
            }

            return;
        }

        // ─── Normal flow ───────────────────────────────────────────
        $this->leadJustCaptured = false;

        $this->messages[] = [
            'role'    => 'user',
            'content' => $text,
            'time'    => now()->format('g:i A'),
        ];
        $this->newMessage = '';
        $this->isThinking = true;

        if (count($this->messages) > $this->maxHistory) {
            $this->messages = array_slice($this->messages, -$this->maxHistory);
        }

        $this->captureLeadIfPresent($text);

        $this->dispatch('message-sent');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Reply                                                      */
    /* ──────────────────────────────────────────────────────────── */

    #[On('message-sent')]
    public function reply(): void
    {
        $contents = collect($this->messages)
            ->map(fn($m) => [
                'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $m['content']]],
            ])
            ->all();

        $apiKey = config('services.gemini.key');
        $url    = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'content-type' => 'application/json',
            ])->timeout(30)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $this->systemPrompt()]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'maxOutputTokens' => 2000,
                    'thinkingConfig'  => [
                        'thinkingLevel' => 'minimal',
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body'   => mb_substr($response->body(), 0, 1000),
                ]);
                $this->pushAssistantMessage(
                    "Sorry, I'm having trouble connecting right now. Please try again, or email contact@polyspheretech.com."
                );
                return;
            }

            $data = $response->json();

            $finishReason = $data['candidates'][0]['finishReason'] ?? null;
            $text         = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($text)) {
                Log::warning('Gemini returned no text', [
                    'finish_reason'   => $finishReason,
                    'usage_metadata'  => $data['usageMetadata'] ?? null,
                    'prompt_feedback' => $data['promptFeedback'] ?? null,
                    'user_message'    => end($this->messages)['content'] ?? null,
                    'body'            => mb_substr($response->body(), 0, 800),
                ]);

                $this->pushAssistantMessage(
                    "I didn't quite catch that — could you rephrase, or email contact@polyspheretech.com so a human can help?"
                );
                return;
            }

            $this->pushAssistantMessage(trim($text));
        } catch (\Throwable $e) {
            Log::error('Chat widget exception', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            $this->pushAssistantMessage('Something went wrong on our end. Please try again shortly.');
        } finally {
            $this->isThinking = false;
        }
    }

    protected function pushAssistantMessage(string $text): void
    {
        $this->messages[] = [
            'role'    => 'assistant',
            'content' => $text,
            'time'    => now()->format('g:i A'),
        ];

        if (! $this->isOpen) {
            $this->hasUnread = true;
        }
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Lead capture                                               */
    /* ──────────────────────────────────────────────────────────── */

    protected function captureLeadIfPresent(string $text): void
    {
        $email = $this->extractEmail($text);
        if (! $email) {
            return;
        }

        $sessionId = session()->getId();
        $newName   = $this->extractName($text);

        // ─── Existing lead in this session? ────────────────────────
        $existing = ChatLead::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            if (strtolower($existing->email) === $email) {
                return;
            }

            $differentPerson = $this->looksLikeDifferentPerson($existing, $newName);

            $isNewEnough = $existing->created_at->gt(now()->subMinutes($this->leadLockMinutes));
            $isUntouched = $existing->status === 'new';

            if (! $differentPerson && $isNewEnough && $isUntouched) {
                $this->updateLeadEmailInPlace($existing, $email);
                return;
            }
        }

        // ─── New lead — evaluate for spam ──────────────────────────
        try {
            $spam   = app(LeadSpamFilter::class)->evaluate($email, $text);
            $isSpam = $spam['is_spam'];
            $reason = $spam['reason'];

            $lead = ChatLead::create([
                'email'        => $email,
                'name'         => $newName,
                'phone'        => $this->extractPhone($text),
                'company'      => $this->extractCompany($text),
                'session_id'   => $sessionId,
                'ip_address'   => request()->ip(),
                'user_agent'   => mb_substr((string) request()->userAgent(), 0, 500),
                'source'       => 'chat-widget',
                'intent'       => $this->inferIntent(),
                'message'      => $text,
                'conversation' => $this->messages,
                'page_url'     => mb_substr((string) request()->header('referer'), 0, 500),
                'status'       => $isSpam ? 'spam' : 'new',
                'is_spam'      => $isSpam,
                'notes'        => $isSpam ? "[Auto-flagged] {$reason}" : null,
            ]);

            // ─── Spam leads: skip all notifications ────────────────
            if ($isSpam) {
                Log::info('Chat lead flagged as spam', [
                    'lead_id' => $lead->id,
                    'email'   => $email,
                    'reason'  => $reason,
                ]);

                return;
            }

            // ─── Genuine lead — notify normally ────────────────────
            $this->leadJustCaptured = true;

            // 1. Notify the team
            Mail::to(NewChatLeadNotification::RECIPIENT)
                ->queue(new NewChatLeadNotification($lead));

            // 2. Acknowledge the visitor — wrapped separately so a failure
            //    here never blocks the admin notification above.
            try {
                Mail::to($lead->email)->queue(new VisitorLeadAcknowledgement($lead));
            } catch (\Throwable $e) {
                Log::warning('Visitor acknowledgement email failed to queue', [
                    'lead_id' => $lead->id,
                    'email'   => $lead->email,
                    'error'   => $e->getMessage(),
                ]);
            }

            $lead->update(['notified_at' => now()]);

            $this->notifyAdminsOfNewLead($lead);
        } catch (\Throwable $e) {
            Log::error('Failed to save chat lead', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function updateLeadEmailInPlace(ChatLead $lead, string $newEmail): void
    {
        try {
            $oldEmail = $lead->email;

            $stamp = now()->format('M j, Y g:i A');
            $note  = "[{$stamp}] Visitor updated their email from {$oldEmail} to {$newEmail}.";

            $lead->email = $newEmail;
            $lead->notes = trim(($lead->notes ? $lead->notes . "\n\n" : '') . $note);

            if (empty($lead->name)) {
                $freshName = $this->extractNameFromMessages();
                if ($freshName) {
                    $lead->name = $freshName;
                }
            }

            if (empty($lead->company)) {
                $freshCompany = $this->extractCompanyFromMessages();
                if ($freshCompany) {
                    $lead->company = $freshCompany;
                }
            }

            if (empty($lead->intent)) {
                $lead->intent = $this->inferIntent();
            }

            $lead->conversation = $this->messages;

            $lead->save();

            Log::info('Chat lead email updated in place', [
                'lead_id'   => $lead->id,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update chat lead email', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    protected function looksLikeDifferentPerson(ChatLead $existing, ?string $newName): bool
    {
        $existingName = trim((string) $existing->name);
        $newName      = trim((string) $newName);

        if ($existingName === '' || $newName === '') {
            return false;
        }

        return strcasecmp($existingName, $newName) !== 0;
    }

    protected function extractNameFromMessages(): ?string
    {
        foreach ($this->messages as $msg) {
            if (($msg['role'] ?? '') === 'user') {
                $name = $this->extractName((string) ($msg['content'] ?? ''));
                if ($name) {
                    return $name;
                }
            }
        }
        return null;
    }

    protected function extractCompanyFromMessages(): ?string
    {
        foreach ($this->messages as $msg) {
            if (($msg['role'] ?? '') === 'user') {
                $company = $this->extractCompany((string) ($msg['content'] ?? ''));
                if ($company) {
                    return $company;
                }
            }
        }
        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Admin notifications for new leads                          */
    /* ──────────────────────────────────────────────────────────── */

    protected function notifyAdminsOfNewLead(ChatLead $lead): void
    {
        if (! class_exists(NotificationHelper::class)) {
            return;
        }

        try {
            $recipients = User::permission('View Chat Leads')->get();
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        if ($recipients->isEmpty()) {
            return;
        }

        $title = $this->buildLeadNotificationTitle($lead);
        $body  = $this->buildLeadNotificationBody($lead);
        $type  = $lead->score >= 70 ? 'success' : 'info';
        $link  = route('admin.leads', ['statusFilter' => 'new']);

        foreach ($recipients as $user) {
            try {
                NotificationHelper::sendToUser($user, [
                    'title' => $title,
                    'body'  => $body,
                    'type'  => $type,
                    'icon'  => 'fa-inbox',
                    'link'  => $link,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    protected function buildLeadNotificationTitle(ChatLead $lead): string
    {
        $who   = $lead->name ?: $lead->email;
        $emoji = $lead->score >= 70 ? '🔥' : '🎯';

        return "{$emoji} New chat lead: {$who}";
    }

    protected function buildLeadNotificationBody(ChatLead $lead): string
    {
        $parts = [];

        $parts[] = $lead->name
            ? "{$lead->name} ({$lead->email})"
            : $lead->email;

        $tier    = strtoupper($lead->score_tier);
        $parts[] = "Score: {$lead->score} ({$tier})";

        if (! empty($lead->message)) {
            $snippet = trim((string) $lead->message);
            if (mb_strlen($snippet) > 120) {
                $snippet = mb_substr($snippet, 0, 120) . '…';
            }
            $parts[] = "\"{$snippet}\"";
        }

        return implode(' • ', $parts);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Lead extraction helpers                                    */
    /* ──────────────────────────────────────────────────────────── */

    protected function extractEmail(string $text): ?string
    {
        if (! preg_match('/\b[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}\b/', $text, $m)) {
            return null;
        }

        $email = strtolower($m[0]);

        $blacklist = [
            'contact@polyspheretech.com',
            'careers@polyspheretech.com',
            'noreply@polyspheretech.com',
            'admin@polyspheretech.com',
            'sales@polyspheretech.com',
        ];

        return in_array($email, $blacklist, true) ? null : $email;
    }

    protected function extractName(string $text): ?string
    {
        if (preg_match('/\b(?:my name is|i am|i\'m|this is|it\'s)\s+([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)?)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    protected function extractPhone(string $text): ?string
    {
        if (preg_match('/(?:\+?\d[\d\s\-\(\)]{7,}\d)/', $text, $m)) {
            $digits = preg_replace('/\D+/', '', $m[0]);
            if (strlen((string) $digits) >= 8 && strlen((string) $digits) <= 15) {
                return trim($m[0]);
            }
        }
        return null;
    }

    /**
     * Extract a company name when the visitor clearly states it.
     */
    protected function extractCompany(string $text): ?string
    {
        $patterns = [
            '/\b(?:i\'m from|im from|i am from)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:i work at|i work for)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:my company is|our company is|company name is|company:)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:we\'re|we are)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $company = trim($m[1]);

                $company = rtrim($company, '.,;:!?');
                $company = preg_replace('/\s+(and|or|but|so|because|and then|the|a|an)$/i', '', $company) ?? $company;
                $company = trim($company);

                if (mb_strlen($company) >= 2 && mb_strlen($company) <= 80) {
                    return $company;
                }
            }
        }

        return null;
    }

    protected function inferIntent(): ?string
    {
        foreach ($this->messages as $msg) {
            if (($msg['role'] ?? '') === 'user') {
                return mb_substr(trim((string) $msg['content']), 0, 500);
            }
        }
        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  View helper — turns bare URLs into clickable links         */
    /* ──────────────────────────────────────────────────────────── */

    public function linkify(string $text): string
    {
        $escaped = e($text);

        return preg_replace(
            '/(https?:\/\/[^\s<>"\']+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $escaped
        );
    }

    public function render()
    {
        return view('livewire.main.chat-widget');
    }
}
