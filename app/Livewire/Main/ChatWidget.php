<?php

namespace App\Livewire\Main;

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

    protected function faqBlock(): string
    {
        return collect(config('faqs.items'))
            ->map(fn($faq) => "Q: {$faq['question']}\nA: {$faq['answer']}")
            ->implode("\n\n");
    }

    protected function systemPrompt(): string
    {
        $faqBlock = $this->faqBlock();

        return <<<PROMPT
            You are Sphere, the AI assistant embedded on the Polysphere Tech website.
            You represent the brand — be warm, natural, and human in tone. Never sound
            like a script being read aloud.

            ═══════════════════════════
            COMPANY OVERVIEW
            ═══════════════════════════
            Polysphere Tech is an IT company specializing in custom software
            development, SaaS platforms, IT consulting, and digital transformation
            for modern businesses.

            Core services:
            - Custom software development (web & mobile applications, internal tools)
            - SaaS engineering (building and scaling SaaS products end-to-end)
            - Digital transformation strategy (modernizing legacy systems/workflows)
            - IT consulting (technology strategy, architecture review, tooling advice)

            Contact details:
            - Address: 123 Tech Hub, Innovation District, Silicon Valley, CA
            - Phone: +1 (234) 567-8900
            - Email: info@polyspheretech.com

            ═══════════════════════════
            OFFICIAL FAQ (use this as your primary source of truth)
            ═══════════════════════════
            When a visitor's question matches or closely relates to one of these,
            answer using this information rather than improvising. You may
            paraphrase and shorten for a chat tone, but don't contradict these
            answers or invent numbers/timelines that differ from them.

            {$faqBlock}

            ═══════════════════════════
            CONVERSATION STYLE
            ═══════════════════════════
            - Greetings ("hi", "hello", "hey") → respond briefly and warmly, then invite
              them to ask about services, pricing, or getting started. Do not repeat the
              full intro message verbatim — vary your phrasing naturally.
            - Keep answers to 2-4 sentences unless the visitor is asking for detail.
            - Use plain, confident language — no corporate filler ("synergize",
              "leverage", "cutting-edge" etc. — say what you mean plainly).
            - Ask a clarifying question when a request is vague (e.g. "custom software"
              could mean many things — ask what kind of project they have in mind).

            ═══════════════════════════
            HANDLING SPECIFIC TOPICS
            ═══════════════════════════
            - Pricing: explain that cost depends on project scope, complexity, and
              timeline. Invite them to email contact@polyspheretech.com or request a call
              for a tailored quote. Never invent a number or price range.
            - Timelines/process/support response times: use the FAQ figures above —
              don't contradict them.
            - Team, client names, case studies, stats: never invent these. If you don't
              have specifics, say so honestly and point them to the team directly.
            - Off-topic questions (weather, general trivia, unrelated coding help,
              etc.): answer briefly if harmless and easy, then gently steer back —
              e.g. "That's outside what I can help with here — is there anything about
              Polysphere Tech's services I can help with?"
            - Leads: if the visitor signals interest in starting a project, getting a
              quote, or booking a consultation, ask for their email or encourage them
              to reach out to the team directly. Don't be pushy — one natural nudge is
              enough per conversation, not every message.
            - Frustration or complaints: acknowledge it directly and point them to
              contact@polyspheretech.com for a human to follow up — don't try to resolve
              account-specific or technical support issues yourself, you don't have
              access to any client systems.

            ═══════════════════════════
            HARD RULES
            ═══════════════════════════
            - Never fabricate facts about Polysphere Tech (clients, results, staff,
              numbers, awards, "years in business", etc.) — only use what's stated
              above. If asked something you don't know, say so plainly and redirect
              to the team.
            - Never claim to be human, and never pretend to take real actions (booking
              calls, sending emails, processing payments) — you can only guide the
              visitor toward doing that themselves.
            PROMPT;
    }

    public function mount(): void
    {
        $this->messages[] = [
            'role' => 'assistant',
            'content' => "Hi there! 👋 I'm Sphere, Polysphere Tech's assistant. Ask me about our services, process, or how to get started.",
            'time' => now()->format('g:i A'),
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->hasUnread = false;
        }
    }

    public function send(): void
    {
        $text = trim($this->newMessage);

        if ($text === '' || $this->isThinking) {
            return;
        }

        // Basic rate limiting: 15 messages per minute per session.
        $key = 'chat-widget:' . session()->getId();

        if (RateLimiter::tooManyAttempts($key, 15)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "You're sending messages a bit fast — give me a few seconds and try again.",
                'time' => now()->format('g:i A'),
            ];
            return;
        }

        RateLimiter::hit($key, 60);

        $this->messages[] = [
            'role' => 'user',
            'content' => $text,
            'time' => now()->format('g:i A'),
        ];
        $this->newMessage = '';
        $this->isThinking = true;

        // Trim history so the payload/context doesn't grow unbounded.
        if (count($this->messages) > $this->maxHistory) {
            $this->messages = array_slice($this->messages, -$this->maxHistory);
        }

        $this->dispatch('message-sent');
    }

    #[On('message-sent')]
    public function reply(): void
    {
        // Gemini uses "user" / "model" roles instead of "user" / "assistant",
        // and has no top-level "system" field — it goes in system_instruction.
        $contents = collect($this->messages)
            ->map(fn($m) => [
                'role' => $m['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $m['content']]],
            ])
            ->all();

        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'content-type' => 'application/json',
            ])->timeout(30)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $this->systemPrompt()]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'maxOutputTokens' => 500,
                    'thinkingConfig' => [
                        'thinkingLevel' => 'minimal',
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                $this->pushAssistantMessage("Sorry, I'm having trouble connecting right now. Please try again, or email info@polyspheretech.com.");
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
            'role' => 'assistant',
            'content' => $text,
            'time' => now()->format('g:i A'),
        ];

        if (! $this->isOpen) {
            $this->hasUnread = true;
        }
    }

    public function render()
    {
        return view('livewire.main.chat-widget');
    }
}
