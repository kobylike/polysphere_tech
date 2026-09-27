<?php

namespace App\Livewire\Main;

use App\Helpers\NotificationHelper;
use App\Mail\NewChatLeadNotification;
use App\Mail\VisitorLeadAcknowledgement;
use App\Models\ChatLead;
use App\Models\User;
use App\Services\ChatKnowledgeBase;
use App\Services\LeadIntentDetector;
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

    public bool $leadJustCaptured = false;

    protected int $maxHistory = 20;

    protected string $model = 'gemini-3.6-flash';

    protected int $leadLockMinutes = 30;

    protected int $sessionRateLimit = 15;

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
    /*  Human handoff short-circuit                                */
    /* ──────────────────────────────────────────────────────────── */

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
    /*  Prompt                                                     */
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
            . "- You may ALSO naturally ask ONE follow-up question to help the team prepare. "
            .   "Pick whichever feels most useful in context:\n"
            .   "    * 'What company are you with?' — if you don't know the company\n"
            .   "    * 'Is there a phone number that works best for a callback?' — for reachability\n"
            .   "    * 'Do you have a rough budget in mind?' — if they're discussing a specific build\n"
            .   "    * 'Is there a timeline you're working towards?' — if the project feels concrete\n"
            .   "    * 'Which industry is this for?' — if the context is unclear\n"
            .   "  Ask ONE only. Do not ask for more than one. Do not sound like a form."
            : '';

        return <<<PROMPT
            You are Sphere, the AI assistant embedded on the Polysphere Tech website.
            You represent the brand — warm, natural, professional, human. Never sound
            like a script or a generic chatbot.

            ═══════════════════════════
            CONTEXT — WHERE YOU ARE
            ═══════════════════════════
            You are embedded as a widget on the Polysphere Tech website. Every
            visitor you talk to is ALREADY ON the site. Never tell them to
            "visit our website" or "go to polyspheretech.com" — they're already
            here. Never share the homepage URL as a suggestion; it's redundant.
            You may share deep links to specific pages (services, projects,
            team, careers, blog) when they're relevant to what the visitor asked.

            ═══════════════════════════
            COMPANY SNAPSHOT
            ═══════════════════════════
            Polysphere Tech is a Ghana-based software engineering firm in Accra,
            building custom software, SaaS platforms, and digital infrastructure
            for startups, SMEs, and established businesses. We work with clients
            across Africa and internationally, and every project is delivered by
            our own in-house team — no outsourcing, no offshore hand-offs.

            Elevator pitch (use as inspiration, don't recite verbatim):
            "We design, build, and maintain the software that runs modern
            businesses — from SaaS platforms to internal tools — with senior
            engineers on every project, transparent pricing, and support after
            launch."

            ═══════════════════════════
            WHAT WE DO
            ═══════════════════════════
            - Custom software development (web apps, mobile apps, internal tools,
              business management systems, portals, dashboards)
            - SaaS engineering (multi-tenant platforms, subscription products,
              from MVP to scale)
            - WordPress & CMS websites (business websites, brochures, content
              sites, WooCommerce stores — we build and customise WordPress
              themes and plugins)
            - Digital transformation (modernizing legacy systems, workflow
              automation, cloud migration)
            - IT consulting (technology strategy, architecture review, security
              audits, digital roadmaps)
            - Systems integration (APIs, payment gateways, third-party platforms)

            Common verticals we build for: fintech, education, healthcare,
            e-commerce, logistics, real estate, hospitality, professional
            services, non-profits, and government.

            ═══════════════════════════
            WHAT WE DON'T DO
            ═══════════════════════════
            Be honest and clear if asked. Do not pretend to offer something we don't.

            - We do NOT sell pre-packaged enterprise software with no customisation.
            - We do NOT do pure staffing / hourly body-shopping. We deliver
              outcomes, not people.
            - We do NOT do hardware, networking, or IT support contracts.
            - We do NOT take projects below a minimum viable scope — if a project
              is too small to do properly, say so honestly and suggest they
              reach out to us again when the scope grows.

            ═══════════════════════════
            HOW WE WORK
            ═══════════════════════════
            Our process for new clients (in order):

            1. Discovery call (30 min, free, no obligation) — understand the
               problem, the users, the outcome they want.
            2. Proposal & scope document — clear deliverables, timeline, and a
               fixed or milestone-based price.
            3. Contract & deposit — sign, pay initial deposit, kick-off.
            4. Build in sprints — typically 2-week sprints with regular demos
               so the client sees progress, not surprises.
            5. Launch, handover, and post-launch support — we deploy, train
               the client's team, and stay on for bug fixes and improvements.

            Our team: a small, senior in-house team of engineers, designers, and
            product managers. Every project has a senior lead — not a junior
            with a title.

            Our tech stack (we choose the right tool per project):
            - Backend: Laravel, Node.js, Python, .NET
            - Frontend: React, Vue.js, Next.js, Tailwind
            - CMS: WordPress (custom themes and plugins), headless WordPress
            - Mobile: React Native, Flutter, native iOS/Android
            - Cloud: AWS, Azure, DigitalOcean
            - Data: MySQL, PostgreSQL, Redis, MongoDB
            - DevOps: Docker, GitHub Actions, CI/CD

            ═══════════════════════════
            BUSINESS FACTS
            ═══════════════════════════
            Business hours: Monday to Friday, 8:00 AM – 6:00 PM GMT.
            Weekend and public-holiday messages are answered on the next business
            day.

            Response times (be realistic, don't over-promise):
            - Chat or email during business hours: usually within a few hours.
            - Outside business hours: on the next business day.
            - For anything urgent, direct visitors to call +233 (59) 756-3427.

            Typical project timelines (rough ranges — never present as a commitment):
            - Landing pages / small internal tools: 4 – 8 weeks
            - WordPress / business websites: 3 – 6 weeks
            - Full web or mobile applications: 8 – 16 weeks
            - SaaS MVPs: 12 – 24 weeks
            - Enterprise platforms: scoped individually after discovery

            Payment terms:
            - Deposit to start (typically 40–50% of project value).
            - Remaining balance paid across agreed milestones.
            - SaaS products: monthly or annual subscription.
            - Consulting: billed hourly or on retainer.

            NDA & confidentiality:
            - Yes, we sign NDAs on request — before the discovery call if needed.
            - All client work is treated as confidential by default.

            Post-launch support:
            - Every project includes a warranty period of bug fixes after launch.
            - Beyond that, we offer ongoing maintenance and support retainers.

            Current availability: we usually can start new engagements within
            2–4 weeks of a signed agreement, depending on team capacity.

            ═══════════════════════════
            CONTACT & CHANNELS
            ═══════════════════════════
            Official contact methods:
            - Phone: +233 (59) 756-3427
            - General email: contact@polyspheretech.com
            - Careers email: careers@polyspheretech.com
            - Address: Accra, Ghana

            Social media (all official Polysphere Tech accounts):
            - LinkedIn:    https://www.linkedin.com/company/polysphere-tech/
            - Facebook:    https://web.facebook.com/polyspheretech
            - Instagram:   https://www.instagram.com/polyspheretech
            - X (Twitter): https://x.com/polyspheretech
            - YouTube:     https://www.youtube.com/@polyspheretech

            When a visitor asks how to reach the team, or which social platforms
            you're on, offer the most relevant 1-3 options naturally. Don't dump
            every channel. If they ask generally "how can I contact you", lead
            with phone + email. If they ask about social media, list all five.
            If they ask specifically about one channel, confirm and share that URL.

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
            - Plain, confident language — no corporate filler ("synergize",
              "leverage", "cutting-edge"). Say what you mean.
            - Ask a clarifying question when the request is vague.
            - Never sound robotic or scripted. Vary your phrasing between replies.
            - If a visitor seems technical, you can go deeper. If non-technical,
              keep things plain and outcome-focused.

            ═══════════════════════════
            PRICING — HOW TO ANSWER
            ═══════════════════════════
            Never invent specific prices, but DO give useful range guidance so
            the visitor doesn't feel stonewalled. Use language like this:

            "Pricing depends on scope, but here's a rough guide. Smaller projects
            — a landing page, a small internal tool — tend to be a few thousand
            dollars. Mid-size builds — a full web or mobile app — usually sit in
            the tens of thousands. Enterprise-grade platforms are scoped
            individually. The fastest way to get a real number is a 30-minute
            discovery call — free and no obligation."

            Rules:
            - Never commit to a specific dollar figure.
            - Never quote a range tighter than "a few thousand" or "tens of
              thousands" — because real numbers depend on scope.
            - Always end a pricing answer with the invitation to book a call or
              share their email so the team can give a real quote.
            - If pressed for a number, politely decline and redirect to the call.
            - For WordPress sites specifically: these typically sit on the lower
              end — a business website or WooCommerce store usually costs less
              than a custom web application. Still don't quote a number.

            ═══════════════════════════
            HANDLING SPECIFIC TOPICS
            ═══════════════════════════
            - "How much does it cost?" → Use the PRICING guidance above.
            - "How long will it take?" → Use the TIMELINES ranges above, then invite
              to a call for a real estimate.
            - "How can I contact you?" → Phone + email first.
            - "Are you on social media?" → List all five with their URLs.
            - "Do you sign NDAs?" → Yes, on request. Explain briefly.
            - "Do you work with international clients?" → Yes — remote-first, we
              work with clients across Africa and internationally.
            - "Can you build me a WordPress site?" → Yes, we do. We build custom
              WordPress themes and plugins, business websites, and WooCommerce
              stores. Timeline is typically 3–6 weeks depending on scope.
            - "Can you do X?" where X isn't in our services → Be honest. If it's
              adjacent to what we do, say so; if not, say we don't and offer what
              we CAN help with.
            - Frustration / complaints → Acknowledge, then point them at
              contact@polyspheretech.com for a human follow-up.
            - Off-topic requests → Politely decline, steer back to Polysphere.

            ═══════════════════════════
            LEAD CAPTURE — HOW TO COLLECT CONTACT DETAILS
            ═══════════════════════════
            Your goal is to help genuine visitors get in touch with the team. Do this
            NATURALLY — never sound like a form or a sales script.

            - Ask for their EMAIL first when they show real interest.
            - Once you have their email, you may ask ONE additional follow-up
              question — not more — to help the team prepare. Pick the most useful:
                * Company name ("What company are you with?")
                * Phone number ("Is there a phone number that works best?")
                * Budget ("Do you have a rough budget in mind?")
                * Timeline ("Is there a timeline you're working towards?")
                * Industry ("Which industry is this for?")
              Do NOT ask more than one. Do NOT sound like a form. Do NOT ask any
              of these if the visitor hasn't yet shown real interest.
            - If they volunteer company, phone, budget, timeline, or industry on
              their own, that's great — you don't need to ask.
            - ONE nudge per conversation. If they decline to share, drop it.

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
            - You may only share URLs from:
                (a) the CONTACT & CHANNELS section above,
                (b) the LIVE KNOWLEDGE BASE below,
                (c) the deep-link list: {$baseUrl}/services, {$baseUrl}/projects,
                    {$baseUrl}/team, {$baseUrl}/careers, {$baseUrl}/blog
            - NEVER tell a visitor to "visit our site" or share {$baseUrl}/ —
              they are already on the site.
            - Write URLs as plain, full, clickable URLs. No markdown link syntax.

            ═══════════════════════════
            HARD RULES
            ═══════════════════════════
            - Never fabricate facts: clients, results, staff, numbers, awards, years
              in business, job openings, team members, projects, or services.
            - Never invent URLs, salaries, dates, or timelines.
            - Never invent specific prices — use the range guidance in PRICING.
            - Never tell a visitor to visit the website they are already on.
            - Never promise a specific delivery date for a new project without a
              signed agreement — always frame timelines as estimates pending a
              discovery call.
            - If you don't know, say so plainly and redirect to the team.
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

            $this->captureLeadIfPresent($text);

            if (! $this->isOpen) {
                $this->hasUnread = true;
            }

            return;
        }

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

            if ($finishReason === 'MAX_TOKENS' && ! empty($text)) {
                Log::warning('Gemini response truncated (MAX_TOKENS)', [
                    'user_message' => end($this->messages)['content'] ?? null,
                    'response_len' => mb_strlen($text),
                    'usage'        => $data['usageMetadata'] ?? null,
                ]);

                $text = rtrim($text, " \t\n\r\0\x0B.,;:-—") . '…';
            }

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

        $existing = ChatLead::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            if (strtolower($existing->email) === $email) {
                $this->mergeIntentIntoLead($existing);
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

        try {
            $spam     = app(LeadSpamFilter::class)->evaluate($email, $text);
            $isSpam   = $spam['is_spam'];
            $reason   = $spam['reason'];
            $softFlag = $spam['soft_flag'] ?? null;

            $intent = $this->detectFullIntent();

            $initialNotes = null;
            if ($isSpam && $reason) {
                $initialNotes = "[Auto-flagged] {$reason}";
            } elseif ($softFlag) {
                $initialNotes = "[⚠ Soft flag] {$softFlag}";
            }

            $lead = ChatLead::create([
                'email'               => $email,
                'name'                => $newName,
                'phone'               => $this->extractPhone($text),
                'company'             => $intent['company'] ?? null,
                'services_interested' => $intent['services_interested'] ?: null,
                'industry'            => $intent['industry'],
                'budget_range'        => $intent['budget_range'],
                'timeline'            => $intent['timeline'],
                'urgency'             => $intent['urgency'],
                'preferred_contact'   => $intent['preferred_contact'],
                'session_id'          => $sessionId,
                'ip_address'          => request()->ip(),
                'user_agent'          => mb_substr((string) request()->userAgent(), 0, 500),
                'source'              => 'chat-widget',
                'intent'              => $this->inferIntent(),
                'message'             => $text,
                'conversation'        => $this->messages,
                'page_url'            => mb_substr((string) request()->header('referer'), 0, 500),
                'status'              => $isSpam ? 'spam' : 'new',
                'is_spam'             => $isSpam,
                'notes'               => $initialNotes,
            ]);

            if ($isSpam) {
                Log::info('Chat lead flagged as spam', [
                    'lead_id' => $lead->id,
                    'email'   => $email,
                    'reason'  => $reason,
                ]);

                return;
            }

            $this->leadJustCaptured = true;

            Mail::to(NewChatLeadNotification::RECIPIENT)
                ->queue(new NewChatLeadNotification($lead));

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

    protected function detectFullIntent(): array
    {
        $transcript = $this->buildUserTranscript();

        return app(LeadIntentDetector::class)->detect($transcript);
    }

    protected function buildUserTranscript(): string
    {
        return collect($this->messages)
            ->filter(fn($m) => ($m['role'] ?? '') === 'user')
            ->pluck('content')
            ->map(fn($c) => trim((string) $c))
            ->filter()
            ->implode("\n");
    }

    protected function mergeIntentIntoLead(ChatLead $lead): void
    {
        try {
            $intent = $this->detectFullIntent();

            $existingServices = $lead->services_interested ?? [];
            $newServices      = $intent['services_interested'] ?? [];
            $mergedServices   = array_values(array_unique(array_merge($existingServices, $newServices)));

            $lead->services_interested = $mergedServices ?: null;
            $lead->industry            = $lead->industry          ?: $intent['industry'];
            $lead->budget_range        = $lead->budget_range      ?: $intent['budget_range'];
            $lead->timeline            = $lead->timeline          ?: $intent['timeline'];
            $lead->urgency             = $lead->urgency           ?: $intent['urgency'];
            $lead->preferred_contact   = $lead->preferred_contact ?: $intent['preferred_contact'];
            $lead->company             = $lead->company           ?: $intent['company'];
            $lead->conversation        = $this->messages;

            $lead->save();
            $lead->rescore();

            Log::info('Chat lead intent refreshed', [
                'lead_id'  => $lead->id,
                'services' => $mergedServices,
                'urgency'  => $lead->urgency,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to refresh chat lead intent', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
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

            $lead->conversation = $this->messages;
            $lead->save();

            $this->mergeIntentIntoLead($lead);

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

    /* ──────────────────────────────────────────────────────────── */
    /*  Admin notifications                                        */
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

        $serviceTag = '';
        if (! empty($lead->services_interested)) {
            $serviceTag = ' · ' . implode(', ', array_slice($lead->services_interested, 0, 2));
        }

        return "{$emoji} New chat lead: {$who}{$serviceTag}";
    }

    protected function buildLeadNotificationBody(ChatLead $lead): string
    {
        $parts = [];

        $parts[] = $lead->name
            ? "{$lead->name} ({$lead->email})"
            : $lead->email;

        $tier    = strtoupper($lead->score_tier);
        $parts[] = "Score: {$lead->score} ({$tier})";

        if (! empty($lead->industry)) {
            $parts[] = "Industry: {$lead->industry}";
        }

        if (! empty($lead->budget_range)) {
            $parts[] = "Budget: {$lead->budget_range}";
        }

        if (! empty($lead->urgency)) {
            $parts[] = 'Urgency: ' . ucfirst($lead->urgency);
        }

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
    /*  Extractors                                                 */
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
