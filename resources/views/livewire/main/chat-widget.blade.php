<div x-data="{
        open: @entangle('isOpen'),
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.scrollArea;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }" x-init="scrollToBottom()" @message-sent.window="scrollToBottom()" x-effect="if (open) scrollToBottom()"
    class="ps-chat-root">
    {{-- Launcher button --}}
    <button wire:click="toggle" class="ps-launcher" x-show="!open" x-transition aria-label="Open chat">
        <i class="fal fa-comment-dots"></i>
        @if($hasUnread)
            <span class="ps-badge"></span>
        @endif
    </button>

    {{-- Chat window --}}
    <div class="ps-window" x-show="open" x-transition:enter="ps-transition-enter"
        x-transition:enter-start="ps-transition-enter-start" x-transition:enter-end="ps-transition-enter-end"
        @click.outside="open = false" style="display: none;">
        <div class="ps-header">
            <div class="ps-header-info">
                <div class="ps-avatar">
                    <i class="fal fa-sparkles"></i>
                </div>
                <div>
                    <div class="ps-header-title">Sphere</div>
                    <div class="ps-header-subtitle">
                        <span class="ps-status-dot"></span> Polysphere Tech Assistant
                    </div>
                </div>
            </div>
            <button wire:click="toggle" class="ps-close" aria-label="Close chat">
                <i class="fal fa-times"></i>
            </button>
        </div>

        <div class="ps-messages" x-ref="scrollArea">
            @foreach($messages as $message)
                <div class="ps-row {{ $message['role'] === 'user' ? 'ps-row-user' : 'ps-row-bot' }}">
                    @if($message['role'] !== 'user')
                        <div class="ps-avatar-sm">
                            <i class="fal fa-sparkles"></i>
                        </div>
                    @endif
                    <div class="ps-bubble-group">
                        <div class="ps-bubble {{ $message['role'] === 'user' ? 'ps-bubble-user' : 'ps-bubble-bot' }}">
                            {{ $message['content'] }}
                        </div>
                        <div class="ps-time {{ $message['role'] === 'user' ? 'ps-time-user' : '' }}">
                            {{ $message['time'] ?? '' }}
                        </div>
                    </div>
                </div>
            @endforeach

            @if($isThinking)
                <div class="ps-row ps-row-bot">
                    <div class="ps-avatar-sm">
                        <i class="fal fa-sparkles"></i>
                    </div>
                    <div class="ps-bubble ps-bubble-bot ps-typing">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            @endif
        </div>

        <form wire:submit="send" class="ps-input-row">
            <input type="text" wire:model="newMessage" placeholder="Ask about Polysphere Tech..." class="ps-input"
                autocomplete="off" @keydown.escape.window="open = false">
            <button type="submit" class="ps-send" wire:loading.attr="disabled" wire:target="send">
                <i class="fal fa-paper-plane"></i>
            </button>
        </form>

        <div class="ps-footer-note">Powered by Gemini &middot; Polysphere Tech</div>
    </div>
</div>

@push('scripts')
    <style>
        .ps-chat-root {
            font-family: inherit;
        }

        .ps-launcher {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 22px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: transform 0.2s ease;
        }

        .ps-launcher:hover {
            transform: scale(1.08);
        }

        .ps-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid #fff;
        }

        .ps-window {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 360px;
            max-width: calc(100vw - 32px);
            height: 520px;
            max-height: calc(100vh - 48px);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
        }

        .ps-transition-enter {
            transition: all 0.25s ease-out;
        }

        .ps-transition-enter-start {
            opacity: 0;
            transform: translateY(16px) scale(0.96);
        }

        .ps-transition-enter-end {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .ps-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .ps-header-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ps-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .ps-header-title {
            font-weight: 700;
            font-size: 15px;
            line-height: 1.2;
        }

        .ps-header-subtitle {
            font-size: 12px;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ps-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #4ade80;
            display: inline-block;
        }

        .ps-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.85;
            padding: 4px;
        }

        .ps-close:hover {
            opacity: 1;
        }

        .ps-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: #f8fafc;
        }

        .ps-messages::-webkit-scrollbar {
            width: 6px;
        }

        .ps-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .ps-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .ps-row-user {
            flex-direction: row-reverse;
        }

        .ps-avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .ps-bubble-group {
            max-width: 78%;
            display: flex;
            flex-direction: column;
        }

        .ps-row-user .ps-bubble-group {
            align-items: flex-end;
        }

        .ps-bubble {
            padding: 10px 14px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .ps-bubble-bot {
            background: #fff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
        }

        .ps-bubble-user {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .ps-time {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 4px;
            padding: 0 4px;
        }

        .ps-time-user {
            text-align: right;
        }

        .ps-typing {
            display: flex;
            gap: 4px;
            padding: 14px;
            align-items: center;
        }

        .ps-typing span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #94a3b8;
            animation: ps-bounce 1.2s infinite ease-in-out;
        }

        .ps-typing span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .ps-typing span:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes ps-bounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.5;
            }

            30% {
                transform: translateY(-4px);
                opacity: 1;
            }
        }

        .ps-input-row {
            display: flex;
            gap: 8px;
            padding: 12px;
            border-top: 1px solid #eef2f6;
            background: #fff;
            flex-shrink: 0;
        }

        .ps-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s;
        }

        .ps-input:focus {
            border-color: #6366f1;
        }

        .ps-send {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.15s;
        }

        .ps-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .ps-footer-note {
            text-align: center;
            font-size: 10.5px;
            color: #94a3b8;
            padding: 6px 0 10px;
            background: #fff;
        }

        @media (max-width: 480px) {
            .ps-window {
                width: calc(100vw - 24px);
                right: 12px;
                bottom: 12px;
            }

            .ps-launcher {
                right: 20px;
                bottom: 20px;
            }
        }
    </style>
@endpush