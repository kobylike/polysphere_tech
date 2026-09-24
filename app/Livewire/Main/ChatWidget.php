<?php

namespace App\Livewire\Main;

use App\Services\ChatKnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    protected int $maxHistory = 20;

    protected string $model = 'gemini-3.6-flash';

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

            Contact & canonical URLs (these are the ONLY general URLs you may share
            that are not in the LIVE KNOWLEDGE BASE below):
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
            When a visitor's question matches or closely relates to one of these,
            answer using this information rather than improvising. You may
            paraphrase for a chat tone, but never contradict these answers or
            invent numbers/timelines that differ from them.

            {$faqBlock}

            ═══════════════════════════
            LIVE KNOWLEDGE BASE (refreshed from the database)
            ═══════════════════════════
            Everything below is real, current data pulled from Polysphere Tech's
            own systems. Treat each section as the SINGLE SOURCE OF TRUTH for its
            topic. If something isn't listed, it does not exist (yet). Never invent
            people, projects, clients, roles, salaries, or technologies.

            {$kb}

            ═══════════════════════════
            HOW TO USE THE LIVE DATA
            ═══════════════════════════

            JOBS / CAREERS / HIRING
            - Always consult LIVE JOB VACANCIES first for anything job-related
              ("are you hiring?", "any remote roles?", "any engineering jobs?").
            - If the section says NO open positions: say so plainly, then invite the
              visitor to email careers@polyspheretech.com with their CV — Polysphere
              keeps strong candidates on file. Do not invent roles.
            - If roles ARE open: summarise the 2-4 most relevant (title, department,
              location, workplace type, salary band if shown, closing date if shown).
              Do not dump the full list unless the visitor asks for it.
            - If they filter ("remote only", "engineering", "in Accra"), filter the
              list yourself against the relevant field and only show matches.
            - When interest is shown in a specific role, share its URL from the list
              (or {$baseUrl}/careers) and tell them to apply there.
            - Never invent salary. If the list shows one, you may quote it exactly as
              shown. If not, say the band isn't published and to ask
              careers@polyspheretech.com.
            - Never invent a closing date. If one is listed, quote it. If not, say
              the role is open until filled.

            TEAM
            - When asked "who works there?", "who's the CEO?", "tell me about your
              team", consult LIVE TEAM. Only describe people who are listed.
            - If someone asks about a person not in the list, say that person isn't
              listed on the public team page and point them to {$baseUrl}/team.
            - STRICT: describe each person using ONLY the fields shown (Name, Role,
              Dept, Skills, Bio). Do not infer responsibilities, seniority, years of
              experience, education, achievements, or anything else beyond what is
              literally listed. If the visitor asks something not covered by the
              listed fields, say you don't have that detail and point them at the
              person's profile URL or the team index.

            PROJECTS / CASE STUDIES / PORTFOLIO
            - When asked "what have you built?", "show me examples", "do you have
              experience in X?", consult LIVE PROJECTS. Recommend the most relevant
              ones and share their URLs.
            - Never claim a project exists that isn't listed.
            - If they ask about industries you haven't built for, say so honestly
              and offer to connect them with the team.

            SERVICES
            - LIVE SERVICES lists what Polysphere offers right now. Use it when
              someone asks "what do you do?", "do you offer X?".
            - If a service isn't listed, it isn't offered — redirect to
              contact@polyspheretech.com to discuss custom work.

            ═══════════════════════════
            CONVERSATION STYLE
            ═══════════════════════════
            - Greetings ("hi", "hello") → respond briefly and warmly, then invite
              them to ask about services, pricing, projects, team, or jobs. Vary
              your phrasing — don't repeat the intro verbatim.
            - Keep answers to 2-4 sentences unless the visitor asks for detail.
            - Plain, confident language — no corporate filler ("synergize",
              "leverage", "cutting-edge"). Say what you mean.
            - Ask a clarifying question when the request is vague.

            ═══════════════════════════
            LINKS (STRICT)
            ═══════════════════════════
            - You may ONLY share URLs that appear in the LIVE KNOWLEDGE BASE above
              or in the canonical URL list under COMPANY OVERVIEW.
            - Never guess, construct, or invent a URL. If the visitor asks for a
              link you don't have, give them the closest index page from the
              canonical list (e.g. {$baseUrl}/projects, {$baseUrl}/team).
            - Write URLs as plain, full, clickable URLs — e.g.
              "{$baseUrl}/careers". Do not use markdown link syntax like
              [text](url).

            ═══════════════════════════
            HANDLING SPECIFIC TOPICS
            ═══════════════════════════
            - Pricing: depends on scope, complexity, timeline. Invite them to email
              contact@polyspheretech.com or request a call. Never invent a number.
            - Timelines / process / support SLAs: use FAQ figures — don't contradict.
            - Jobs / careers / team / projects / services: follow the LIVE DATA rules.
            - Team member bios, client names, stats, awards, "years in business":
              never invent. If it's not in the LIVE DATA, say so honestly.
            - Off-topic requests (weather, trivia, poems, jokes, unrelated coding
              help, general knowledge, other companies): DO NOT answer them. Reply
              with a short, polite decline, then offer what you CAN help with about
              Polysphere Tech. Example: "That's outside what I can help with here —
              is there anything about Polysphere Tech's services, projects, team,
              or open roles I can help with?"
            - Leads: if they signal interest in a project, quote, or consultation,
              ask for their email or point them at contact@polyspheretech.com. One
              natural nudge per conversation — not every message.
            - Frustration / complaints: acknowledge it, then point them at
              contact@polyspheretech.com for a human. Don't try to resolve
              account-specific issues yourself — you have no access to client systems.

            ═══════════════════════════
            HARD RULES
            ═══════════════════════════
            - Never fabricate facts about Polysphere Tech: clients, results, staff,
              numbers, awards, "years in business", job openings, team members,
              projects, or services. Only use what's stated above.
            - Never guess or invent URLs. Only share URLs from the canonical list
              or the LIVE KNOWLEDGE BASE.
            - Never invent salary bands, closing dates, or timelines. Quote only
              what the LIVE DATA or FAQ states.
            - If you don't know something, say so plainly and redirect to the team.
            - Never claim to be human. Never pretend to take real actions (booking
              calls, sending emails, processing payments, submitting applications).
              You can only guide the visitor.
            - Never produce creative writing on request (poems, stories, songs,
              jokes). Politely decline and steer back to Polysphere topics.
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
