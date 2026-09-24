<?php

namespace App\Livewire\Main;

use App\Helpers\NotificationHelper;
use App\Mail\NewChatLeadNotification;
use App\Models\ChatLead;
use App\Models\User;
use App\Services\ChatKnowledgeBase;
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

    /**
     * A lead stays "editable in place" (for typo corrections / follow-up
     * emails in the same chat) for this many minutes. Past this window, a
     * different email creates a fresh lead instead of overwriting.
     */
    protected int $leadLockMinutes = 30;

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
            . "- If they asked a question in the same message, still answer it briefly."
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
            LINKS (STRICT)
            ═══════════════════════════
            - Only share URLs from the canonical list above or the LIVE KNOWLEDGE BASE.
            - Write URLs as plain, full, clickable URLs. No markdown link syntax.

            ═══════════════════════════
            HANDLING SPECIFIC TOPICS
            ═══════════════════════════
            - Pricing: depends on scope, complexity, timeline. Invite them to share
              their email OR contact@polyspheretech.com. Never invent a number.
            - Leads: if the visitor signals interest in a project, quote, or consultation,
              ask for their email naturally — ONE nudge per conversation, not every
              message. Example: "Happy to have someone reach out — what's a good email
              for you?"
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
    /*  Send / reply                                               */
    /* ──────────────────────────────────────────────────────────── */

    public function send(): void
    {
        $text = trim($this->newMessage);

        if ($text === '' || $this->isThinking) {
            return;
        }

        $key = 'chat-widget:' . session()->getId();

        if (RateLimiter::tooManyAttempts($key, 15)) {
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => "You're sending messages a bit fast — give me a few seconds and try again.",
                'time'    => now()->format('g:i A'),
            ];
            return;
        }

        RateLimiter::hit($key, 60);

        // Reset each turn — captureLeadIfPresent() sets it back to true if
        // this message is the one that creates a brand-new lead.
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

        // Detect + persist lead BEFORE calling Gemini, so the bot's reply
        // can acknowledge the capture in the same turn.
        $this->captureLeadIfPresent($text);

        $this->dispatch('message-sent');
    }

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
                    'maxOutputTokens' => 800,
                    'thinkingConfig'  => [
                        'thinkingLevel' => 'minimal',
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                $this->pushAssistantMessage(
                    "Sorry, I'm having trouble connecting right now. Please try again, or email contact@polyspheretech.com."
                );
                return;
            }

            $data = $response->json();

            $text = $data['candidates'][0]['content']['parts'][0]['text']
                ?? "Sorry, I didn't quite catch that — could you rephrase?";

            $this->pushAssistantMessage(trim($text));
        } catch (\Throwable $e) {
            Log::error('Chat widget exception', ['message' => $e->getMessage()]);
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

    /**
     * Rule set:
     *   1. No email in the message                 → do nothing.
     *   2. No lead in this session                 → create a new lead + notify.
     *   3. Same email as existing session lead     → do nothing.
     *   4. Different email, different person name  → create a NEW lead.
     *   5. Different email, existing lead is:
     *        - status === 'new'
     *        - created < 30 min ago
     *        - not a different person
     *                                              → UPDATE existing lead's email
     *                                                + log change in notes.
     *   6. Different email, existing lead is
     *      contacted OR older than 30 min         → create a NEW lead.
     */
    protected function captureLeadIfPresent(string $text): void
    {
        $email = $this->extractEmail($text);
        if (! $email) {
            return;
        }

        $sessionId = session()->getId();
        $newName   = $this->extractName($text);

        // ─── Look for an existing lead in this session ─────────────
        $existing = ChatLead::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            // Same email → nothing to do.
            if (strtolower($existing->email) === $email) {
                return;
            }

            // Different email — is this a different person?
            $differentPerson = $this->looksLikeDifferentPerson($existing, $newName);

            // Same session, same lead, same "shift" → fold in.
            $isNewEnough = $existing->created_at->gt(now()->subMinutes($this->leadLockMinutes));
            $isUntouched = $existing->status === 'new';

            if (! $differentPerson && $isNewEnough && $isUntouched) {
                $this->updateLeadEmailInPlace($existing, $email);
                return;
            }

            // Otherwise fall through to create a fresh lead.
        }

        // ─── Create a brand-new lead ───────────────────────────────
        try {
            $lead = ChatLead::create([
                'email'        => $email,
                'name'         => $newName,
                'phone'        => $this->extractPhone($text),
                'company'      => null,
                'session_id'   => $sessionId,
                'ip_address'   => request()->ip(),
                'user_agent'   => mb_substr((string) request()->userAgent(), 0, 500),
                'source'       => 'chat-widget',
                'intent'       => $this->inferIntent(),
                'message'      => $text,
                'conversation' => $this->messages,
                'page_url'     => mb_substr((string) request()->header('referer'), 0, 500),
            ]);

            $this->leadJustCaptured = true;

            Mail::to(NewChatLeadNotification::RECIPIENT)
                ->queue(new NewChatLeadNotification($lead));

            $lead->update(['notified_at' => now()]);

            $this->notifyAdminsOfNewLead($lead);
        } catch (\Throwable $e) {
            Log::error('Failed to save chat lead', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Fold a corrected / secondary email into the existing lead rather than
     * forking a duplicate. Adds an audit trail to notes so admins see
     * exactly what changed and when.
     */
    protected function updateLeadEmailInPlace(ChatLead $lead, string $newEmail): void
    {
        try {
            $oldEmail = $lead->email;

            $stamp = now()->format('M j, Y g:i A');
            $note  = "[{$stamp}] Visitor updated their email from {$oldEmail} to {$newEmail}.";

            $lead->email = $newEmail;
            $lead->notes = trim(($lead->notes ? $lead->notes . "\n\n" : '') . $note);

            // Fill in name if the existing lead didn't have one yet.
            if (empty($lead->name)) {
                $freshName = $this->extractNameFromMessages();
                if ($freshName) {
                    $lead->name = $freshName;
                }
            }

            // Keep the "intent" fresh if it wasn't set yet.
            if (empty($lead->intent)) {
                $lead->intent = $this->inferIntent();
            }

            // Refresh conversation snapshot so the admin sees the latest thread.
            $lead->conversation = $this->messages;

            $lead->save();

            // Deliberately NOT setting $this->leadJustCaptured. The bot should
            // not say "I've got your details" a second time — it already said
            // it when the lead was first created.

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

    /**
     * Decide whether a second email in the same session looks like a
     * different person rather than the same visitor correcting themselves.
     *
     * Logic:
     *   - If the existing lead has NO name → cannot tell → assume same person.
     *   - If the new message has NO name  → cannot tell → assume same person.
     *   - If both names exist and match (case-insensitive) → same person.
     *   - If both names exist and differ   → different person.
     */
    protected function looksLikeDifferentPerson(ChatLead $existing, ?string $newName): bool
    {
        $existingName = trim((string) $existing->name);
        $newName      = trim((string) $newName);

        if ($existingName === '' || $newName === '') {
            return false;
        }

        return strcasecmp($existingName, $newName) !== 0;
    }

    /**
     * Scan the conversation history for the first name the visitor used.
     * Used to backfill `name` when a lead was created from an email-only
     * message and the visitor introduces themselves later.
     */
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
