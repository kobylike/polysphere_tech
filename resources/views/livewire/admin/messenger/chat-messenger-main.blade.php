<div id="ptp-chat-app" x-data="pageChatState(
        @js($friendIds),
        @js($friends->pluck('name', 'id')->toArray()),
        @js($showStarredOnly),
        {{ $activeFriendId ?? 'null' }}
     )" x-init="init()" wire:ignore.self :data-panel="activePanel" :data-tier="tier" x-cloak>

    <style>
        [x-cloak] {
            display: none !important;
        }

        #ptp-chat-app {
            --ptp-bg: #eef2f7;
            --ptp-surface: #ffffff;
            --ptp-surface-alt: #f8fafc;
            --ptp-border: #e2e8f0;
            --ptp-border-soft: #eef2f7;
            --ptp-text: #0f172a;
            --ptp-text-mid: #475569;
            --ptp-text-muted: #94a3b8;
            --ptp-accent: #6366f1;
            --ptp-accent-2: #8b5cf6;
            --ptp-accent-soft: #eef2ff;
            --ptp-danger: #ef4444;
            --ptp-success: #10b981;
            --ptp-warning: #f59e0b;

            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            color: var(--ptp-text);
            display: block;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        #ptp-chat-app *,
        #ptp-chat-app *::before,
        #ptp-chat-app *::after {
            box-sizing: border-box;
        }

        /* ══ SHELL ════════════════════════════════════════════════════════ */
        #ptp-chat-app .ptp-wrap {
            background: var(--ptp-bg);
            padding: 16px;
            min-height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        #ptp-chat-app .ptp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 4px;
        }

        #ptp-chat-app .ptp-topbar h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-topbar p {
            font-size: 13px;
            color: var(--ptp-text-muted);
            margin: 2px 0 0;
        }

        #ptp-chat-app .ptp-topbar .ptp-breadcrumb {
            font-size: 12.5px;
            color: var(--ptp-text-muted);
        }

        #ptp-chat-app .ptp-topbar .ptp-breadcrumb a {
            color: var(--ptp-accent);
            text-decoration: none;
        }

        #ptp-chat-app .ptp-shell {
            background: var(--ptp-surface);
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 12px 32px -12px rgba(15, 23, 42, .08);
            overflow: hidden;
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr) 320px;
            height: calc(100vh - 160px);
            min-height: 560px;
            width: 100%;
        }

        #ptp-chat-app .ptp-pane {
            display: flex;
            flex-direction: column;
            min-height: 0;
            min-width: 0;
            overflow: hidden;
            background: var(--ptp-surface);
        }

        #ptp-chat-app .ptp-pane--contacts {
            border-right: 1px solid var(--ptp-border-soft);
        }

        #ptp-chat-app .ptp-pane--chat {
            border-right: 1px solid var(--ptp-border-soft);
        }

        #ptp-chat-app .ptp-pane--info {
            overflow-y: auto;
            background: var(--ptp-surface-alt);
        }

        /* ═══════════════════════════════════════════════════════════════
           CHAT PANE — STICKY HEADER + COMPOSER, SCROLLING MESSAGES
           Only the messages area scrolls. Header, search bar, pinned
           bar, attachment preview, reply bar, and composer stay
           locked to the top / bottom of the pane. Works on EVERY
           breakpoint (desktop / tablet / mobile).
        ═══════════════════════════════════════════════════════════════ */
        #ptp-chat-app .ptp-pane--chat {
            height: 100% !important;
            min-height: 0 !important;
            max-height: 100% !important;
            overflow: hidden !important;
            flex-direction: column !important;
        }

        #ptp-chat-app .ptp-pane--chat>.d-flex.flex-column.h-100 {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 auto !important;
            height: 100% !important;
            min-height: 0 !important;
            max-height: 100% !important;
            overflow: hidden !important;
        }

        /* Every direct child of the chat wrapper holds its natural size… */
        #ptp-chat-app .ptp-pane--chat>.d-flex.flex-column.h-100>* {
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
            flex-basis: auto !important;
        }

        /* …except the messages area, which grows and scrolls internally. */
        #ptp-chat-app .ptp-pane--chat>.d-flex.flex-column.h-100>.chat-box-area {
            flex: 1 1 auto !important;
            flex-grow: 1 !important;
            flex-shrink: 1 !important;
            flex-basis: 0 !important;
            min-height: 0 !important;
            max-height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* ══ LEFT PANE — contacts ═════════════════════════════════════════ */
        #ptp-chat-app .ptp-me {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--ptp-border-soft);
        }

        #ptp-chat-app .ptp-me__avatar {
            position: relative;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-me__avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        #ptp-chat-app .ptp-me__dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--ptp-success);
            border: 2px solid var(--ptp-surface);
        }

        #ptp-chat-app .ptp-me__meta {
            min-width: 0;
            flex: 1;
        }

        #ptp-chat-app .ptp-me__name {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            color: var(--ptp-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ptp-chat-app .ptp-me__sub {
            font-size: 12px;
            color: var(--ptp-text-muted);
            margin: 2px 0 0;
        }

        #ptp-chat-app .ptp-me__badge {
            background: var(--ptp-accent-soft);
            color: var(--ptp-accent);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
        }

        #ptp-chat-app .ptp-search {
            padding: 14px 16px 10px;
        }

        #ptp-chat-app .ptp-search__box {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--ptp-surface-alt);
            border: 1px solid var(--ptp-border-soft);
            border-radius: 12px;
            padding: 0 12px;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }

        #ptp-chat-app .ptp-search__box:focus-within {
            border-color: var(--ptp-accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
        }

        #ptp-chat-app .ptp-search__box svg {
            color: var(--ptp-text-muted);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-search__input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 10px 8px;
            font-size: 13px;
            color: var(--ptp-text);
            font-family: inherit;
        }

        #ptp-chat-app .ptp-search__input::placeholder {
            color: var(--ptp-text-muted);
        }

        #ptp-chat-app .ptp-people {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 6px 8px 16px;
            scrollbar-width: thin;
            scrollbar-color: var(--ptp-border) transparent;
        }

        #ptp-chat-app .ptp-people::-webkit-scrollbar {
            width: 8px;
        }

        #ptp-chat-app .ptp-people::-webkit-scrollbar-thumb {
            background: var(--ptp-border);
            border-radius: 4px;
            border: 2px solid var(--ptp-surface);
        }

        #ptp-chat-app .ptp-person {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: background .15s, transform .1s;
            -webkit-tap-highlight-color: transparent;
            position: relative;
        }

        #ptp-chat-app .ptp-person:hover {
            background: var(--ptp-surface-alt);
        }

        #ptp-chat-app .ptp-person:active {
            transform: scale(.99);
        }

        #ptp-chat-app .ptp-person.is-active {
            background: var(--ptp-accent-soft);
        }

        #ptp-chat-app .ptp-person.is-active .ptp-person__name {
            color: var(--ptp-accent);
        }

        #ptp-chat-app .ptp-person__avatar {
            position: relative;
            width: 46px;
            height: 46px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-person__avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 2px solid transparent;
            transition: border-color .15s;
        }

        #ptp-chat-app .ptp-person.is-active .ptp-person__avatar img {
            border-color: var(--ptp-accent);
        }

        #ptp-chat-app .ptp-person__dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--ptp-text-muted);
            border: 2px solid var(--ptp-surface);
        }

        #ptp-chat-app .ptp-person__dot.is-online {
            background: var(--ptp-success);
        }

        #ptp-chat-app .ptp-person.is-active .ptp-person__dot {
            border-color: var(--ptp-accent-soft);
        }

        #ptp-chat-app .ptp-person__meta {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        #ptp-chat-app .ptp-person__name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ptp-text);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ptp-chat-app .ptp-person__preview {
            font-size: 12px;
            color: var(--ptp-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ptp-chat-app .ptp-person__preview.is-unread {
            color: var(--ptp-text-mid);
            font-weight: 600;
        }

        #ptp-chat-app .ptp-person__side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-person__time {
            font-size: 10.5px;
            color: var(--ptp-text-muted);
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        #ptp-chat-app .ptp-person__pill {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--ptp-accent);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(99, 102, 241, .35);
        }

        /* ══ MIDDLE — header ══════════════════════════════════════════════ */
        #ptp-chat-app .ptp-chat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--ptp-border-soft);
            background: var(--ptp-surface);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-back {
            display: inline-flex;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: none;
            background: var(--ptp-surface-alt);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--ptp-text-mid);
            flex-shrink: 0;
            transition: background .15s, color .15s;
        }

        #ptp-chat-app .ptp-back:hover {
            background: var(--ptp-border-soft);
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-chat-header__avatar {
            position: relative;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-chat-header__avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        #ptp-chat-app .ptp-chat-header__dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--ptp-text-muted);
            border: 2px solid var(--ptp-surface);
        }

        #ptp-chat-app .ptp-chat-header__dot.is-online {
            background: var(--ptp-success);
        }

        #ptp-chat-app .ptp-chat-header__meta {
            flex: 1;
            min-width: 0;
        }

        #ptp-chat-app .ptp-chat-header__name {
            font-size: 15px;
            font-weight: 700;
            color: var(--ptp-text);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ptp-chat-app .ptp-chat-header__sub {
            font-size: 12px;
            color: var(--ptp-text-muted);
            margin: 2px 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ptp-chat-app .ptp-chat-header__sub.is-online {
            color: var(--ptp-success);
            font-weight: 500;
        }

        #ptp-chat-app .ptp-chat-header__sub.is-typing {
            color: var(--ptp-accent);
            font-weight: 500;
        }

        #ptp-chat-app .ptp-chat-header__actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-iconbtn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--ptp-text-mid);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s, color .15s;
            flex-shrink: 0;
            font-size: 14px;
        }

        #ptp-chat-app .ptp-iconbtn:hover {
            background: var(--ptp-surface-alt);
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-iconbtn.is-starred {
            color: var(--ptp-warning);
        }

        #ptp-chat-app .ptp-iconbtn--accent {
            background: var(--ptp-accent);
            color: #fff;
        }

        #ptp-chat-app .ptp-iconbtn--accent:hover {
            background: var(--ptp-accent-2);
            color: #fff;
        }

        #ptp-chat-app .ptp-iconbtn--success {
            background: var(--ptp-success);
            color: #fff;
        }

        #ptp-chat-app .ptp-iconbtn--success:hover {
            background: #059669;
            color: #fff;
        }

        #ptp-chat-app .ptp-csearch {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: var(--ptp-surface-alt);
            border-bottom: 1px solid var(--ptp-border-soft);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-csearch svg {
            color: var(--ptp-text-muted);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-csearch input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 13px;
            font-family: inherit;
            color: var(--ptp-text);
            min-width: 0;
        }

        #ptp-chat-app .ptp-csearch button {
            border: none;
            background: transparent;
            color: var(--ptp-text-muted);
            width: 28px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #ptp-chat-app .ptp-csearch button:hover {
            background: var(--ptp-border-soft);
            color: var(--ptp-text);
        }

        /* ══ MESSAGES ════════════════════════════════════════════════════ */
        #ptp-chat-app .chat-box-area {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            height: auto !important;
            max-height: none !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            overscroll-behavior: contain !important;
            position: relative !important;
            padding: 16px 22px 8px !important;
            background: var(--ptp-surface) !important;
        }

        #ptp-chat-app .msg-row {
            position: relative !important;
        }

        #ptp-chat-app .msg-row .msg-actions {
            position: absolute !important;
            top: -12px !important;
            right: 8px !important;
            display: flex !important;
            gap: 4px !important;
            padding: 4px 6px !important;
            background: #fff !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12) !important;
            opacity: 0 !important;
            transition: opacity .15s !important;
            pointer-events: none !important;
            z-index: 5 !important;
        }

        #ptp-chat-app .msg-row:hover .msg-actions {
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        #ptp-chat-app .msg-row.mine .msg-actions {
            right: auto !important;
            left: 8px !important;
        }

        #ptp-chat-app .msg-action-btn {
            position: static !important;
            border: none !important;
            background: transparent !important;
            padding: 4px 6px !important;
            border-radius: 50% !important;
            font-size: .9rem !important;
            color: #666 !important;
            cursor: pointer !important;
        }

        #ptp-chat-app .msg-action-btn:hover {
            background: #f1f3f5 !important;
            color: #111 !important;
        }

        #ptp-chat-app .reaction-pills {
            display: flex !important;
            gap: 4px !important;
            flex-wrap: wrap !important;
            margin-top: -6px !important;
            position: relative !important;
            z-index: 2 !important;
        }

        #ptp-chat-app .reaction-pill {
            position: static !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 1px 8px !important;
            font-size: .75rem !important;
            background: #fff !important;
            border: 1px solid #e9ecef !important;
            border-radius: 20px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06) !important;
            cursor: pointer !important;
        }

        #ptp-chat-app .reaction-pill.mine {
            background: #e7f3ff !important;
            border-color: #bfe0ff !important;
        }

        #ptp-chat-app .quote-bubble {
            background: rgba(0, 0, 0, .05) !important;
            border-left: 3px solid #0d99ff !important;
            padding: 6px 10px !important;
            margin-bottom: 6px !important;
            border-radius: 6px !important;
            font-size: .8rem !important;
        }

        #ptp-chat-app .msg-row.mine .quote-bubble {
            border-left-color: #fff !important;
            background: rgba(255, 255, 255, .15) !important;
        }

        #ptp-chat-app .msg-row.same-sender .avatar-spacer {
            width: 34px !important;
        }

        #ptp-chat-app .chat-link {
            color: #0d99ff !important;
            text-decoration: underline !important;
            word-break: break-all !important;
        }

        #ptp-chat-app .message-sent .chat-link {
            color: #fff !important;
            text-decoration: underline !important;
        }

        #ptp-chat-app .pinned-bar {
            background: #fffbe6 !important;
            border-bottom: 1px solid #ffe89a !important;
            padding: 6px 12px !important;
            font-size: .8rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-shrink: 0 !important;
        }

        /* Empty states */
        #ptp-chat-app .ptp-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
            color: var(--ptp-text-muted);
            gap: 12px;
        }

        #ptp-chat-app .ptp-empty__icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--ptp-accent-soft);
            color: var(--ptp-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        #ptp-chat-app .ptp-empty h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--ptp-text);
            margin: 0;
        }

        #ptp-chat-app .ptp-empty p {
            font-size: 13px;
            margin: 0;
            max-width: 260px;
            line-height: 1.5;
        }

        /* Search-in-chat empty state */
        #ptp-chat-app .ptp-search-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--ptp-text-muted);
        }

        #ptp-chat-app .ptp-search-empty__icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--ptp-surface-alt);
            color: var(--ptp-text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
        }

        #ptp-chat-app .ptp-search-empty h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--ptp-text);
            margin: 0 0 4px;
        }

        #ptp-chat-app .ptp-search-empty p {
            font-size: 12.5px;
            margin: 0;
        }

        #ptp-chat-app .ptp-search-empty .ptp-search-empty__q {
            color: var(--ptp-accent);
            font-weight: 600;
        }

        /* Reply bar */
        #ptp-chat-app .ptp-replybar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            background: var(--ptp-surface-alt);
            border-top: 1px solid var(--ptp-border-soft);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-replybar__bar {
            width: 3px;
            align-self: stretch;
            min-height: 34px;
            background: var(--ptp-accent);
            border-radius: 2px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-replybar__meta {
            flex: 1;
            min-width: 0;
        }

        #ptp-chat-app .ptp-replybar__title {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ptp-accent);
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        #ptp-chat-app .ptp-replybar__body {
            font-size: 13px;
            color: var(--ptp-text-mid);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ══ COMPOSER ═════════════════════════════════════════════════════ */
        #ptp-chat-app .ptp-composer {
            position: relative;
            padding: 12px 20px 16px;
            background: var(--ptp-surface);
            border-top: 1px solid var(--ptp-border-soft);
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-composer__row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        #ptp-chat-app .ptp-composer__left {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-composer__btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: none;
            background: transparent;
            color: var(--ptp-text-mid);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background .15s, color .15s;
            flex-shrink: 0;
        }

        #ptp-chat-app .ptp-composer__btn:hover {
            background: var(--ptp-surface-alt);
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-composer__btn.is-recording {
            background: #fee2e2;
            color: var(--ptp-danger);
        }

        #ptp-chat-app .ptp-composer__field {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            background: var(--ptp-surface-alt);
            border: 1px solid var(--ptp-border-soft);
            border-radius: 22px;
            padding: 4px 4px 4px 16px;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }

        #ptp-chat-app .ptp-composer__field:focus-within {
            border-color: var(--ptp-accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .1);
        }

        #ptp-chat-app .ptp-composer__field textarea {
            flex: 1;
            min-width: 0;
            border: none;
            outline: none;
            resize: none;
            background: transparent;
            padding: 9px 0;
            font-family: inherit;
            font-size: 13.5px;
            line-height: 1.5;
            color: var(--ptp-text);
            max-height: 120px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        #ptp-chat-app .ptp-composer__field textarea::placeholder {
            color: var(--ptp-text-muted);
        }

        #ptp-chat-app .ptp-composer__send {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, var(--ptp-accent) 0%, var(--ptp-accent-2) 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .1s, box-shadow .15s;
            flex-shrink: 0;
            box-shadow: 0 4px 12px -4px rgba(99, 102, 241, .45);
            font-size: 13px;
        }

        #ptp-chat-app .ptp-composer__send:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px -4px rgba(99, 102, 241, .55);
        }

        #ptp-chat-app .ptp-composer__send:active {
            transform: scale(.94);
        }

        #ptp-chat-app .ptp-recording {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            padding: 8px 16px;
            background: #fee2e2;
            border-radius: 22px;
        }

        #ptp-chat-app .ptp-recording__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--ptp-danger);
            animation: ptp-pulse 1s ease-in-out infinite;
        }

        #ptp-chat-app .ptp-recording__time {
            font-size: 13.5px;
            font-weight: 600;
            color: #991b1b;
            font-variant-numeric: tabular-nums;
        }

        #ptp-chat-app .ptp-recording__cancel {
            margin-left: auto;
            padding: 6px 14px;
            border-radius: 999px;
            border: none;
            background: #fff;
            color: #991b1b;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        @keyframes ptp-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .35;
                transform: scale(.8);
            }
        }

        /* Popovers */
        #ptp-chat-app .ptp-pop {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 20px;
            z-index: 40;
            background: var(--ptp-surface);
            border: 1px solid var(--ptp-border);
            border-radius: 16px;
            box-shadow: 0 12px 40px -8px rgba(15, 23, 42, .18), 0 4px 12px rgba(15, 23, 42, .06);
            animation: ptp-pop .18s ease;
        }

        @keyframes ptp-pop {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        #ptp-chat-app .ptp-pop--emoji {
            padding: 4px;
        }

        #ptp-chat-app .ptp-pop--attach {
            padding: 8px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
            width: 240px;
        }

        #ptp-chat-app .ptp-pop--sticker {
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 2px;
            width: 280px;
            max-height: 220px;
            overflow-y: auto;
        }

        #ptp-chat-app .ptp-pop__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ptp-text-mid);
            transition: background .12s, color .12s;
            text-align: left;
        }

        #ptp-chat-app .ptp-pop__item:hover {
            background: var(--ptp-surface-alt);
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-pop__item i {
            color: var(--ptp-accent);
            width: 16px;
            text-align: center;
        }

        #ptp-chat-app .ptp-pop__sticker {
            border: none;
            background: transparent;
            font-size: 24px;
            padding: 6px;
            border-radius: 8px;
            cursor: pointer;
            transition: background .12s, transform .1s;
            line-height: 1;
        }

        #ptp-chat-app .ptp-pop__sticker:hover {
            background: var(--ptp-surface-alt);
            transform: scale(1.15);
        }

        /* ══ INFO PANE ════════════════════════════════════════════════════ */
        #ptp-chat-app .ptp-info__head {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 20px 20px;
            text-align: center;
            border-bottom: 1px solid var(--ptp-border-soft);
        }

        #ptp-chat-app .ptp-info__avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 12px;
            border: 3px solid var(--ptp-surface);
            box-shadow: 0 8px 24px -8px rgba(15, 23, 42, .15);
        }

        #ptp-chat-app .ptp-info__name {
            font-size: 16px;
            font-weight: 700;
            color: var(--ptp-text);
            margin: 0;
        }

        #ptp-chat-app .ptp-info__sub {
            font-size: 12px;
            color: var(--ptp-text-muted);
            margin: 4px 0 0;
        }

        #ptp-chat-app .ptp-info__sub.is-online {
            color: var(--ptp-success);
        }

        #ptp-chat-app .ptp-info__section {
            padding: 16px 20px;
            border-bottom: 1px solid var(--ptp-border-soft);
        }

        #ptp-chat-app .ptp-info__section:last-child {
            border-bottom: none;
        }

        #ptp-chat-app .ptp-info__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--ptp-text-muted);
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #ptp-chat-app .ptp-info__title span {
            color: var(--ptp-text-muted);
            font-weight: 500;
            font-size: 10.5px;
        }

        #ptp-chat-app .ptp-media-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        #ptp-chat-app .ptp-media-grid a {
            display: block;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: var(--ptp-surface-alt);
            transition: transform .15s;
        }

        #ptp-chat-app .ptp-media-grid a:hover {
            transform: scale(1.04);
        }

        #ptp-chat-app .ptp-media-grid img,
        #ptp-chat-app .ptp-media-grid video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        #ptp-chat-app .ptp-file {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--ptp-surface);
            border: 1px solid var(--ptp-border-soft);
            margin-bottom: 6px;
            text-decoration: none;
            color: var(--ptp-text);
            transition: background .15s, border-color .15s;
        }

        #ptp-chat-app .ptp-file:hover {
            background: #fff;
            border-color: var(--ptp-accent);
        }

        #ptp-chat-app .ptp-file__icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--ptp-accent-soft);
            color: var(--ptp-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        #ptp-chat-app .ptp-file__meta {
            flex: 1;
            min-width: 0;
        }

        #ptp-chat-app .ptp-file__name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ptp-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }

        #ptp-chat-app .ptp-file__date {
            font-size: 11px;
            color: var(--ptp-text-muted);
            margin: 1px 0 0;
        }

        #ptp-chat-app .ptp-voice {
            background: var(--ptp-surface);
            border: 1px solid var(--ptp-border-soft);
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 6px;
        }

        #ptp-chat-app .ptp-voice audio {
            width: 100%;
            height: 32px;
            outline: none;
        }

        #ptp-chat-app .ptp-voice__date {
            font-size: 11px;
            color: var(--ptp-text-muted);
            margin-top: 4px;
        }

        #ptp-chat-app .ptp-info__empty {
            font-size: 12px;
            color: var(--ptp-text-muted);
            margin: 0;
            font-style: italic;
        }

        /* Context menu */
        #ptp-chat-app .ptp-ctx {
            position: fixed;
            z-index: 9999;
            min-width: 220px;
            background: var(--ptp-surface);
            border: 1px solid var(--ptp-border);
            border-radius: 14px;
            box-shadow: 0 20px 50px -12px rgba(15, 23, 42, .25), 0 8px 16px rgba(15, 23, 42, .06);
            padding: 6px;
            display: none;
        }

        #ptp-chat-app .ptp-ctx.show {
            display: block;
            animation: ptp-pop .15s ease;
        }

        #ptp-chat-app .ptp-ctx__row {
            display: flex;
            justify-content: space-around;
            padding: 6px 4px;
            border-bottom: 1px solid var(--ptp-border-soft);
            margin-bottom: 4px;
        }

        #ptp-chat-app .ptp-ctx__row button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: transparent;
            font-size: 22px;
            cursor: pointer;
            transition: background .12s, transform .12s;
            line-height: 1;
        }

        #ptp-chat-app .ptp-ctx__row button:hover {
            background: var(--ptp-surface-alt);
            transform: scale(1.15);
        }

        #ptp-chat-app .ptp-ctx__item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 9px 12px;
            border: none;
            background: transparent;
            text-align: left;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            color: var(--ptp-text-mid);
            cursor: pointer;
            transition: background .12s, color .12s;
        }

        #ptp-chat-app .ptp-ctx__item i {
            width: 16px;
            text-align: center;
            color: var(--ptp-text-muted);
        }

        #ptp-chat-app .ptp-ctx__item:hover {
            background: var(--ptp-surface-alt);
            color: var(--ptp-text);
        }

        #ptp-chat-app .ptp-ctx__item:hover i {
            color: var(--ptp-accent);
        }

        #ptp-chat-app .ptp-ctx__item.is-danger {
            color: var(--ptp-danger);
        }

        #ptp-chat-app .ptp-ctx__item.is-danger i {
            color: var(--ptp-danger);
        }

        #ptp-chat-app .ptp-ctx__item.is-danger:hover {
            background: #fee2e2;
        }

        #ptp-chat-app .ptp-ctx__sep {
            height: 1px;
            background: var(--ptp-border-soft);
            margin: 4px 6px;
        }

        #ptp-chat-app .ptp-drop {
            position: absolute;
            inset: 12px;
            border: 2px dashed var(--ptp-accent);
            border-radius: 16px;
            background: rgba(99, 102, 241, .06);
            color: var(--ptp-accent);
            font-weight: 600;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 30;
            pointer-events: none;
            flex-direction: column;
            gap: 12px;
        }

        #ptp-chat-app .ptp-drop.is-active {
            display: flex;
        }

        /* ══ RESPONSIVE ══════════════════════════════════════════════════ */
        @media (max-width: 1199.98px) {
            #ptp-chat-app .ptp-shell {
                grid-template-columns: 300px minmax(0, 1fr);
            }

            #ptp-chat-app .ptp-pane--info {
                display: none !important;
            }
        }

        @media (max-width: 767.98px) {
            #ptp-chat-app .ptp-wrap {
                padding: 0;
                min-height: 100vh;
            }

            #ptp-chat-app .ptp-topbar {
                display: none;
            }

            #ptp-chat-app .ptp-shell {
                grid-template-columns: 1fr;
                border-radius: 0;
                box-shadow: none;
                height: 100vh;
                min-height: 0;
            }

            #ptp-chat-app .ptp-pane--contacts,
            #ptp-chat-app .ptp-pane--chat {
                grid-column: 1;
                grid-row: 1;
                border-right: none;
            }

            #ptp-chat-app .ptp-pane--chat {
                display: none;
            }

            #ptp-chat-app[data-panel="chat"] .ptp-pane--chat {
                display: flex;
            }

            #ptp-chat-app[data-panel="chat"] .ptp-pane--contacts {
                display: none;
            }

            #ptp-chat-app .ptp-composer {
                padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
            }

            #ptp-chat-app .ptp-pop {
                left: 12px;
                right: 12px;
                width: auto !important;
            }

            #ptp-chat-app .ptp-pop--sticker {
                grid-template-columns: repeat(6, 1fr);
            }

            #ptp-chat-app .ptp-ctx {
                left: 8px !important;
                right: 8px !important;
                top: auto !important;
                bottom: calc(8px + env(safe-area-inset-bottom, 0px)) !important;
                min-width: 0;
                max-height: 70vh;
                overflow-y: auto;
            }

            #ptp-chat-app .ptp-person {
                padding: 12px;
            }

            #ptp-chat-app .ptp-person__avatar {
                width: 48px;
                height: 48px;
            }

            #ptp-chat-app .ptp-search__input,
            #ptp-chat-app .ptp-csearch input,
            #ptp-chat-app .ptp-composer__field textarea {
                font-size: 16px;
            }

            #ptp-chat-app .msg-row .msg-actions {
                opacity: 1 !important;
                pointer-events: auto !important;
                top: 4px !important;
                right: 4px !important;
            }

            #ptp-chat-app .msg-row.mine .msg-actions {
                left: 4px !important;
                right: auto !important;
            }

            #ptp-chat-app .msg-action-btn {
                padding: 8px 10px !important;
                font-size: 1rem !important;
            }
        }

        @media (max-width: 479.98px) {
            #ptp-chat-app .chat-box-area {
                padding: 12px 14px 6px !important;
            }

            #ptp-chat-app .ptp-composer {
                padding: 10px 12px 12px;
            }

            #ptp-chat-app .ptp-composer__btn {
                width: 36px;
                height: 36px;
            }

            #ptp-chat-app .ptp-composer__field {
                padding-left: 14px;
            }

            #ptp-chat-app .ptp-composer__send {
                width: 34px;
                height: 34px;
            }

            #ptp-chat-app .ptp-chat-header {
                padding: 12px 14px;
            }

            #ptp-chat-app .ptp-me {
                padding: 14px 16px;
            }

            #ptp-chat-app .ptp-search {
                padding: 12px 14px 8px;
            }

            #ptp-chat-app .ptp-people {
                padding: 4px 6px 14px;
            }
        }

        @supports (padding: env(safe-area-inset-bottom)) {
            #ptp-chat-app {
                padding-left: env(safe-area-inset-left, 0);
                padding-right: env(safe-area-inset-right, 0);
            }
        }
    </style>

    <div class="ptp-wrap">
        {{-- PAGE TITLES --}}
        <div class="page-titles">
            <ol class="breadcrumb">
                <li>
                    <h5 class="bc-title">Messenger</h5>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Messenger</a></li>
            </ol>
            <div class="d-flex gap-2">


            </div>
        </div>

        <div class="ptp-shell">

            {{-- CONTACTS --}}
            <aside class="ptp-pane ptp-pane--contacts">
                <div class="ptp-me">
                    <div class="ptp-me__avatar">
                        <img src="{{ auth()->user()->getAvatarUrlAttribute() }}" alt="">
                        <span class="ptp-me__dot"></span>
                    </div>
                    <div class="ptp-me__meta">
                        <h4 class="ptp-me__name">{{ auth()->user()->name }}</h4>
                        <p class="ptp-me__sub">You're online</p>
                    </div>
                    @if($totalReceivedMessages > 0)
                        <span class="ptp-me__badge">{{ $totalReceivedMessages }} new</span>
                    @endif
                </div>

                <div class="ptp-search">
                    <div class="ptp-search__box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <input type="text" class="ptp-search__input" x-model="search" @input="filterFriends()"
                            placeholder="Search conversations">
                    </div>
                </div>

                <div class="ptp-people">
                    @foreach($friends as $friend)
                        <div class="ptp-person {{ $activeFriendId === $friend->id ? 'is-active' : '' }}"
                            wire:key="page-friend-{{ $friend->id }}"
                            x-show="isVisible({{ $friend->id }}, '{{ addslashes(strtolower($friend->name)) }}')"
                            role="button" tabindex="0" @click="selectFriend({{ $friend->id }})"
                            @keydown.enter.prevent="selectFriend({{ $friend->id }})">
                            <div class="ptp-person__avatar">
                                <img src="{{ $friend->getAvatarUrlAttribute() }}" alt="">
                                <span class="ptp-person__dot {{ $friend->online ? 'is-online' : '' }}"></span>
                            </div>
                            <div class="ptp-person__meta">
                                <h5 class="ptp-person__name">{{ $friend->name }}</h5>
                                <span class="ptp-person__preview {{ $friend->message_count > 0 ? 'is-unread' : '' }}">
                                    @if($friend->last_message_type === 'image') 📷 Photo
                                    @elseif($friend->last_message_type === 'document') 📄 Document
                                    @elseif($friend->last_message_type === 'audio') 🎵 Audio
                                    @elseif($friend->last_message_type === 'video') 🎬 Video
                                    @elseif($friend->last_message_type === 'sticker') Sticker
                                    @elseif($friend->last_message_body)
                                        {{ $friend->last_message_is_mine ? 'You: ' : '' }}{{ Str::limit($friend->last_message_body, 34) }}
                                    @else
                                        {{ $friend->online ? 'Active now' : 'Last seen ' . $friend->lastSeenForHumans() }}
                                    @endif
                                </span>
                            </div>
                            <div class="ptp-person__side">
                                @if($friend->message_count > 0)
                                    <span
                                        class="ptp-person__pill">{{ $friend->message_count > 99 ? '99+' : $friend->message_count }}</span>
                                @elseif($friend->last_message_time)
                                    <span
                                        class="ptp-person__time">{{ $friend->last_message_time->diffForHumans(null, true) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($friends->isEmpty())
                        <div class="ptp-empty" style="padding: 60px 20px;">
                            <div class="ptp-empty__icon"><i class="fa-regular fa-comments"></i></div>
                            <h3>No conversations yet</h3>
                            <p>Your contacts will appear here once they start chatting with you.</p>
                        </div>
                    @endif
                </div>
            </aside>

            {{-- ═══ CONVERSATION ═══ --}}
            <section class="ptp-pane ptp-pane--chat position-relative" x-data="{ dragging: false }"
                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                @drop.prevent="dragging = false; handleDrop($event)">

                @php $selectedFriend = $activeFriendId ? $friends->firstWhere('id', $activeFriendId) : null; @endphp

                @if($selectedFriend)
                    <div wire:key="page-chat-{{ $activeFriendId }}" class="d-flex flex-column h-100" style="min-height:0;">

                        {{-- Header --}}
                        <header class="ptp-chat-header">
                            <button type="button" class="ptp-back" @click.stop.prevent="onBack()" aria-label="Back">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6" />
                                </svg>
                            </button>
                            <div class="ptp-chat-header__avatar">
                                <img src="{{ $selectedFriend->getAvatarUrlAttribute() }}" alt="">
                                <span class="ptp-chat-header__dot {{ $selectedFriend->online ? 'is-online' : '' }}"></span>
                            </div>
                            <div class="ptp-chat-header__meta">
                                <h3 class="ptp-chat-header__name">{{ $selectedFriend->name }}</h3>
                                <p
                                    class="ptp-chat-header__sub {{ $selectedFriend->online ? 'is-online' : '' }} {{ $this->isFriendTyping() ? 'is-typing' : '' }}">
                                    @if($this->isFriendTyping()) Typing…
                                    @elseif($selectedFriend->online) Active now
                                    @else Last seen {{ $selectedFriend->lastSeenForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="ptp-chat-header__actions" wire:ignore @click.stop>
                                <button type="button" class="ptp-iconbtn" @click.stop.prevent="openSearch()" title="Search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <button type="button" class="ptp-iconbtn {{ $showStarredOnly ? 'is-starred' : '' }}"
                                    @click.stop.prevent="toggleStarred()" title="Starred">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                                <button type="button" class="ptp-iconbtn ptp-iconbtn--success" title="Audio call"
                                    @click.stop.prevent="startCall('audio', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                    <i class="fa-solid fa-phone"></i>
                                </button>
                                <button type="button" class="ptp-iconbtn ptp-iconbtn--accent" title="Video call"
                                    @click.stop.prevent="startCall('video', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                    <i class="fa-solid fa-video"></i>
                                </button>
                            </div>
                        </header>

                        {{-- ═══ SEARCH BAR — REAL-TIME ═══ --}}
                        <div class="ptp-csearch" wire:ignore x-show="searchOpen" x-cloak>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-3.5-3.5" />
                            </svg>
                            <input type="text" x-ref="searchInput" x-model="searchQuery"
                                @input.debounce.300ms="liveSearch()" @keydown.enter.prevent.stop="liveSearch()"
                                @keydown.escape.prevent.stop="closeSearch()" @click.stop @mousedown.stop
                                placeholder="Search in conversation…" autocomplete="off">
                            <button type="button" @click.stop.prevent="closeSearch()" title="Clear & close">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        @if($this->pinnedMessages->isNotEmpty())
                            <div class="pinned-bar">
                                <i class="fa-solid fa-thumbtack text-warning"></i>
                                <strong>Pinned:</strong>
                                <span class="text-truncate" style="max-width:340px;">
                                    {{ $this->pinnedMessages->first()->preview }}
                                </span>
                            </div>
                        @endif

                        {{-- ═══ MESSAGES ═══ --}}
                        <div class="chat-box-area style-2 dz-scroll" id="DZ_Page_Messages_Body">

                            @if($hasMoreMessages && !$searchQuery)
                                <div class="text-center my-3">
                                    <button type="button" class="btn btn-sm btn-light" wire:click.stop.prevent="loadMore"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="loadMore">Load earlier messages</span>
                                        <span wire:loading wire:target="loadMore">Loading…</span>
                                    </button>
                                </div>
                            @endif

                            @php
                                $newBadgeInserted = false;
                                $prevSenderId = null;
                                $prevCreated = null;
                            @endphp

                            @foreach($this->messages as $message)
                                @if(!$newBadgeInserted && $firstUnreadMessageId && $message->id == $firstUnreadMessageId && !$searchQuery)
                                    @php $newBadgeInserted = true; @endphp
                                    <div class="text-center my-3">
                                        <span class="badge bg-primary-subtle text-primary">New messages</span>
                                    </div>
                                @endif

                                @php
                                    $isMine = $message->sender_id === auth()->id();
                                    $sameSender = $prevSenderId === $message->sender_id
                                        && $prevCreated
                                        && $message->created_at->diffInSeconds($prevCreated) < 180;
                                    $prevSenderId = $message->sender_id;
                                    $prevCreated = $message->created_at;
                                    $reactions = $message->reactionSummary(auth()->id());
                                    $isStarred = $message->isStarredBy(auth()->id());
                                @endphp

                                <div class="media msg-row {{ $isMine ? 'justify-content-end align-items-end ms-auto mine' : '' }} {{ $sameSender ? 'same-sender' : '' }}"
                                    wire:key="page-msg-{{ $message->id }}">

                                    @if(!$isMine)
                                        @if(!$sameSender)
                                            <img src="{{ $selectedFriend->getAvatarUrlAttribute() }}" class="avatar rounded-circle"
                                                style="width:34px;height:34px;" alt="">
                                        @else
                                            <div class="avatar-spacer"></div>
                                        @endif
                                    @endif

                                    <div class="{{ $isMine ? 'message-sent' : 'message-received' }} w-auto">

                                        <div class="msg-actions" @click.stop>
                                            <button type="button" class="msg-action-btn"
                                                @click.stop="quickReact($event, {{ $message->id }})" title="React">😊</button>
                                            <button type="button" class="msg-action-btn"
                                                wire:click.stop.prevent="setReply({{ $message->id }})" title="Reply">
                                                <i class="fa-solid fa-reply"></i>
                                            </button>
                                            <button type="button" class="msg-action-btn"
                                                @click.stop="openContextMenu($event, {{ $message->id }}, {{ $isMine ? 'true' : 'false' }}, {{ $message->deleted_for_everyone ? 'true' : 'false' }})"
                                                title="More">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                        </div>

                                        @if($message->deleted_for_everyone)
                                            <p class="mb-1 fst-italic opacity-50">
                                                <i class="fa-solid fa-ban"></i> This message was deleted
                                            </p>
                                        @else
                                            @if($message->replyTo)
                                                <div class="quote-bubble">
                                                    <strong>{{ $message->replyTo->sender_id === auth()->id() ? 'You' : $selectedFriend->name }}</strong><br>
                                                    <span class="text-truncate d-inline-block" style="max-width:220px;">
                                                        {{ $message->replyTo->preview }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if($message->is_forwarded)
                                                <div class="small opacity-75 mb-1">
                                                    <i class="fa-solid fa-share"></i> Forwarded
                                                </div>
                                            @endif

                                            @if($message->attachment_type === 'sticker')
                                                <span style="font-size:2.5rem;">{{ $message->attachment_path }}</span>
                                            @elseif($message->attachment_type === 'image')
                                                <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $message->attachment_path) }}"
                                                        style="max-width:220px;border-radius:8px;display:block;cursor:zoom-in;">
                                                </a>
                                                @if($message->body)
                                                <p class="mb-0 mt-1">{!! $message->rendered_body !!}</p>@endif
                                            @elseif($message->attachment_type === 'video')
                                                <video controls style="max-width:240px;border-radius:8px;">
                                                    <source src="{{ asset('storage/' . $message->attachment_path) }}">
                                                </video>
                                                @if($message->body)
                                                <p class="mb-0 mt-1">{!! $message->rendered_body !!}</p>@endif
                                            @elseif($message->attachment_type === 'audio')
                                                <div class="voice-msg d-flex align-items-center gap-2">
                                                    @if($message->is_voice)
                                                        <i class="fa-solid fa-microphone text-primary"></i>
                                                        <audio controls style="max-width:220px;"
                                                            src="{{ asset('storage/' . $message->attachment_path) }}"></audio>
                                                        <span class="small text-muted">
                                                            {{ $message->metadata['duration'] ?? 0 }}s
                                                        </span>
                                                    @else
                                                        <audio controls style="width:100%;"
                                                            src="{{ asset('storage/' . $message->attachment_path) }}"></audio>
                                                    @endif
                                                </div>
                                            @elseif($message->attachment_type === 'document')
                                                <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"
                                                    class="d-block text-decoration-none">
                                                    <i class="fa-solid fa-file-lines fa-lg"></i>
                                                    <span class="ms-1">{{ $message->attachment_name ?? 'Document' }}</span>
                                                </a>
                                                @if($message->body)
                                                <p class="mb-0 mt-1">{!! $message->rendered_body !!}</p>@endif
                                            @else
                                                <p class="mb-1">{!! $message->rendered_body !!}</p>
                                            @endif

                                            <span class="fs-12 d-flex align-items-center justify-content-end gap-1">
                                                @if($message->is_pinned)<i class="fa-solid fa-thumbtack text-warning"></i>@endif
                                                @if($isStarred)<i class="fa-solid fa-star text-warning"></i>@endif
                                                @if($message->edited_at)<span class="opacity-75">edited</span>@endif
                                                {{ $message->created_at->format('h:i A') }}
                                                @if($isMine)
                                                    @if($message->read)
                                                        <i class="fa-solid fa-check-double tick read"></i>
                                                    @elseif($message->delivered_at)
                                                        <i class="fa-solid fa-check-double tick"></i>
                                                    @else
                                                        <i class="fa-solid fa-check tick"></i>
                                                    @endif
                                                @endif
                                            </span>
                                        @endif

                                        @if(!empty($reactions))
                                            <div class="reaction-pills mt-1">
                                                @foreach($reactions as $r)
                                                    <button type="button" class="reaction-pill {{ $r['mine'] ? 'mine' : '' }}"
                                                        wire:click.stop.prevent="toggleReaction({{ $message->id }}, '{{ $r['emoji'] }}')">
                                                        {{ $r['emoji'] }} <span>{{ $r['count'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if($this->messages->isEmpty())
                                @if($searchQuery)
                                    <div class="ptp-search-empty">
                                        <div class="ptp-search-empty__icon">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <h4>No matches found</h4>
                                        <p>Nothing matches <span class="ptp-search-empty__q">"{{ $searchQuery }}"</span></p>
                                    </div>
                                @elseif($showStarredOnly)
                                    <div class="ptp-search-empty">
                                        <div class="ptp-search-empty__icon">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                        <h4>No starred messages</h4>
                                        <p>Star messages to find them here later.</p>
                                    </div>
                                @else
                                    <p class="text-center text-muted mt-4">
                                        Say hi to {{ $selectedFriend->name }} 👋
                                    </p>
                                @endif
                            @endif
                        </div>

                        {{-- Attachment preview --}}
                        @if($showAttachmentPreview)
                            <div class="d-flex align-items-center px-3 py-2 border-top" style="gap:10px; flex-shrink:0;">
                                @if($attachmentType === 'image')
                                    <img src="{{ $attachmentPreview }}"
                                        style="width:44px;height:44px;object-fit:cover;border-radius:8px;">
                                @else
                                    <div
                                        style="width:44px;height:44px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fa-solid fa-file"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div style="font-weight:600;font-size:.85rem;">{{ $attachmentName }}</div>
                                    <div style="font-size:.75rem;color:#888;">{{ $attachmentType }}</div>
                                </div>
                                <button type="button" wire:click.stop.prevent="clearAttachment" @mousedown.prevent
                                    class="btn btn-sm btn-light">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Reply / edit bar --}}
                        @if($replyToId || $editingId)
                            @php $ctxMsg = \App\Models\Message::find($editingId ?? $replyToId); @endphp
                            <div class="ptp-replybar">
                                <div class="ptp-replybar__bar"></div>
                                <div class="ptp-replybar__meta">
                                    <div class="ptp-replybar__title">{{ $editingId ? 'Editing' : 'Replying to' }}</div>
                                    <div class="ptp-replybar__body">{{ $ctxMsg?->preview }}</div>
                                </div>
                                <button type="button" class="ptp-iconbtn"
                                    wire:click.stop.prevent="{{ $editingId ? 'cancelEdit' : 'cancelReply' }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Composer --}}
                        <div class="ptp-composer">
                            <div class="ptp-composer__row">
                                <div class="ptp-composer__left">
                                    <button type="button" class="ptp-composer__btn" @mousedown.prevent
                                        @click.stop.prevent="showEmoji = !showEmoji" title="Emoji">
                                        <i class="fa-regular fa-face-smile"></i>
                                    </button>
                                    <button type="button" class="ptp-composer__btn" @mousedown.prevent
                                        @click.stop.prevent="showAttachMenu = !showAttachMenu" title="Attach">
                                        <i class="fa-solid fa-paperclip"></i>
                                    </button>
                                    <button type="button" class="ptp-composer__btn" :class="{ 'is-recording': recording }"
                                        @mousedown.prevent @click.stop.prevent="toggleRecording()" title="Voice">
                                        <i class="fa-solid" :class="recording ? 'fa-stop' : 'fa-microphone'"></i>
                                    </button>
                                </div>

                                <template x-if="!recording">
                                    <div class="ptp-composer__field">
                                        <textarea rows="1" x-model="messageText" placeholder="Type a message…"
                                            @keydown="handleKeydown($event)" @input="startTyping()"
                                            @paste="handlePaste($event)" x-ref="input"></textarea>
                                        <button type="button" class="ptp-composer__send" @mousedown.prevent
                                            wire:click.stop.prevent="sendMessage" @click="onSend()">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </template>

                                <template x-if="recording">
                                    <div class="ptp-recording">
                                        <span class="ptp-recording__dot"></span>
                                        <span class="ptp-recording__time" x-text="recordingTimeFormatted">00:00</span>
                                        <button type="button" class="ptp-recording__cancel"
                                            @click.stop.prevent="cancelRecording()">
                                            Cancel
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div class="ptp-pop ptp-pop--emoji" x-show="showEmoji" x-cloak @click.away="showEmoji = false">
                                <emoji-picker @emoji-click="addEmoji($event)"></emoji-picker>
                            </div>

                            <div class="ptp-pop ptp-pop--attach" x-show="showAttachMenu" x-cloak
                                @click.away="showAttachMenu = false">
                                <label class="ptp-pop__item">
                                    <i class="fa-solid fa-image"></i> Photo
                                    <input type="file" wire:model="attachment" accept="image/*" class="d-none" hidden>
                                </label>
                                <label class="ptp-pop__item">
                                    <i class="fa-solid fa-video"></i> Video
                                    <input type="file" wire:model="attachment" accept="video/*" class="d-none" hidden>
                                </label>
                                <label class="ptp-pop__item">
                                    <i class="fa-solid fa-music"></i> Audio
                                    <input type="file" wire:model="attachment" accept="audio/*" class="d-none" hidden>
                                </label>
                                <label class="ptp-pop__item">
                                    <i class="fa-solid fa-file-lines"></i> Document
                                    <input type="file" wire:model="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                                        class="d-none" hidden>
                                </label>
                                <button type="button" class="ptp-pop__item" @mousedown.prevent
                                    @click.stop.prevent="showStickers = !showStickers" style="grid-column: span 2;">
                                    <i class="fa-regular fa-face-smile"></i> Stickers
                                </button>
                            </div>

                            <div class="ptp-pop ptp-pop--sticker" x-show="showStickers" x-cloak
                                @click.away="showStickers = false">
                                @foreach(['😂', '😍', '🥰', '😎', '🤩', '😭', '🙏', '👏', '🔥', '💯', '❤️', '💔', '🎉', '🎊', '✨', '🌟', '😤', '😴', '🤔', '🥳', '🤯', '😱', '👀', '💪'] as $sticker)
                                    <button type="button" class="ptp-pop__sticker" @mousedown.prevent
                                        wire:click.stop.prevent="sendSticker('{{ $sticker }}')"
                                        @click="showStickers = false">{{ $sticker }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="ptp-drop" :class="{ 'is-active': dragging }">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:36px;"></i>
                        <div>Drop file to send</div>
                    </div>
                @else
                    <div class="ptp-empty">
                        <div class="ptp-empty__icon"><i class="fa-regular fa-comments"></i></div>
                        <h3>Your messages</h3>
                        <p>Select a conversation from the list to start chatting.</p>
                    </div>
                @endif
            </section>

            {{-- INFO --}}
            <aside class="ptp-pane ptp-pane--info">
                @if($selectedFriend)
                    <div class="ptp-info__head">
                        <img src="{{ $selectedFriend->getAvatarUrlAttribute() }}" class="ptp-info__avatar" alt="">
                        <h3 class="ptp-info__name">{{ $selectedFriend->name }}</h3>
                        <p class="ptp-info__sub {{ $selectedFriend->online ? 'is-online' : '' }}">
                            {{ $selectedFriend->online ? 'Active now' : 'Last seen ' . $selectedFriend->lastSeenForHumans() }}
                        </p>
                    </div>

                    <div class="ptp-info__section">
                        <h4 class="ptp-info__title">Media <span>{{ $this->mediaMessages->count() }}</span></h4>
                        @if($this->mediaMessages->isNotEmpty())
                            <div class="ptp-media-grid">
                                @foreach($this->mediaMessages->take(9) as $m)
                                    <a href="{{ asset('storage/' . $m->attachment_path) }}" target="_blank">
                                        @if($m->attachment_type === 'video')
                                            <video>
                                                <source src="{{ asset('storage/' . $m->attachment_path) }}">
                                            </video>
                                        @else
                                            <img src="{{ asset('storage/' . $m->attachment_path) }}" alt="">
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="ptp-info__empty">No photos or videos yet.</p>
                        @endif
                    </div>

                    <div class="ptp-info__section">
                        <h4 class="ptp-info__title">Files <span>{{ $this->fileMessages->count() }}</span></h4>
                        @forelse($this->fileMessages as $f)
                            <a href="{{ asset('storage/' . $f->attachment_path) }}" target="_blank" class="ptp-file">
                                <div class="ptp-file__icon"><i class="fa-solid fa-file-lines"></i></div>
                                <div class="ptp-file__meta">
                                    <p class="ptp-file__name">{{ $f->attachment_name ?? 'Document' }}</p>
                                    <p class="ptp-file__date">{{ $f->created_at->format('M j, Y') }}</p>
                                </div>
                            </a>
                        @empty
                            <p class="ptp-info__empty">No files shared yet.</p>
                        @endforelse
                    </div>

                    <div class="ptp-info__section">
                        <h4 class="ptp-info__title">Voice Notes <span>{{ $this->voiceMessages->count() }}</span></h4>
                        @forelse($this->voiceMessages as $v)
                            <div class="ptp-voice">
                                <audio controls preload="metadata" src="{{ asset('storage/' . $v->attachment_path) }}"></audio>
                                <div class="ptp-voice__date">{{ $v->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="ptp-info__empty">No voice notes yet.</p>
                        @endforelse
                    </div>
                @else
                    <div class="ptp-empty" style="padding: 60px 20px;">
                        <div class="ptp-empty__icon"><i class="fa-regular fa-folder-open"></i></div>
                        <h3>Shared content</h3>
                        <p>Media, files and voice notes from a conversation will show up here.</p>
                    </div>
                @endif
            </aside>
        </div>
    </div>

    <div class="ptp-ctx" id="ctx-menu" x-data="ctxMenuState()" @click.away="close()">
        <div class="ptp-ctx__row">
            <template x-for="emoji in ['👍','❤️','😂','😮','😢','🙏']" :key="emoji">
                <button type="button" @click.stop.prevent="react(emoji)"><span x-text="emoji"></span></button>
            </template>
        </div>
        <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('reply')"><i
                class="fa-solid fa-reply"></i> Reply</button>
        <template x-if="isMine && !isDeleted">
            <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('edit')"><i
                    class="fa-solid fa-pen"></i> Edit</button>
        </template>
        <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('forward')"><i
                class="fa-solid fa-share"></i> Forward</button>
        <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('copy')"><i
                class="fa-solid fa-copy"></i> Copy</button>
        <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('star')"><i
                class="fa-solid fa-star"></i> Star / Unstar</button>
        <button type="button" class="ptp-ctx__item" @click.stop.prevent="doAction('pin')"><i
                class="fa-solid fa-thumbtack"></i> Pin / Unpin</button>
        <div class="ptp-ctx__sep"></div>
        <button type="button" class="ptp-ctx__item is-danger" @click.stop.prevent="doAction('deleteMe')">
            <i class="fa-solid fa-trash"></i> Delete for me
        </button>
        <template x-if="isMine && !isDeleted">
            <button type="button" class="ptp-ctx__item is-danger" @click.stop.prevent="doAction('deleteAll')">
                <i class="fa-solid fa-trash-can"></i> Delete for everyone
            </button>
        </template>
    </div>

    @livewire('admin.messenger.call-overlays')

    @push('scripts')
        <script>
            window.pageChatState = function pageChatState(initialFriendIds, friendNames, initialStarred, initialFriendId) {
                const startTier = (() => {
                    const w = window.innerWidth;
                    if (w < 768) return 'mobile';
                    if (w < 1200) return 'tablet';
                    return 'desktop';
                })();

                return {
                    tier: startTier,
                    activePanel: (startTier === 'mobile' && initialFriendId) ? 'chat' : 'contacts',
                    searchOpen: false,
                    showStarredOnly: initialStarred === true,
                    searchQuery: '',
                    showEmoji: false,
                    showAttachMenu: false,
                    showStickers: false,
                    isTyping: false,
                    messageText: @entangle('messageText').live,
                    friendIds: initialFriendIds,
                    friendNames: friendNames,
                    search: '',
                    filteredCount: initialFriendIds.length,
                    activeFriendId: initialFriendId,
                    switchingFriend: false,
                    recording: false,
                    mediaRecorder: null,
                    audioChunks: [],
                    recordingStart: 0,
                    recordingTimeFormatted: '00:00',
                    timerInterval: null,
                    _swipeStartX: 0,
                    _swipeStartY: 0,

                    init() {
                        this.recording = false;
                        if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; }
                        this.setViewportHeight();
                        this.recomputeTier();
                        window.addEventListener('resize', () => { this.setViewportHeight(); this.recomputeTier(); });
                        window.addEventListener('orientationchange', () => setTimeout(() => this.setViewportHeight(), 150));
                        if (window.visualViewport) {
                            window.visualViewport.addEventListener('resize', () => this.setViewportHeight());
                            window.visualViewport.addEventListener('scroll', () => this.setViewportHeight());
                        }
                        this.$watch('searchOpen', (open) => {
                            if (open) this.$nextTick(() => this.$refs.searchInput?.focus());
                        });
                        if (window.ChatBridge && window.ChatBridge.subscribeToProfileUpdates) {
                            window.ChatBridge.subscribeToProfileUpdates(this.friendIds);
                        }
                        const self = this;
                        this.$watch('friendIds', function (newIds) {
                            if (window.ChatBridge && window.ChatBridge.subscribeToProfileUpdates) {
                                window.ChatBridge.subscribeToProfileUpdates(newIds);
                            }
                            self.filterFriends();
                        });
                        window.addEventListener('update-profile-subscriptions', function (e) {
                            if (e.detail && e.detail.friendIds) {
                                self.friendIds = e.detail.friendIds;
                                self.filterFriends();
                            }
                        });
                        window.addEventListener('friend-selected', function (e) {
                            if (e.detail && e.detail.friendId) {
                                self.activeFriendId = e.detail.friendId;
                                if (window.ChatBridge) window.ChatBridge.subscribeToChat(e.detail.friendId);
                            }
                            if (self.tier === 'mobile') self.activePanel = 'chat';
                            // Reset in-chat search state on friend switch
                            self.searchOpen = false;
                            self.searchQuery = '';
                            self.$wire.set('searchQuery', '');
                        });
                        setInterval(() => {
                            if (self.messageText && self.messageText.trim()) {
                                self.$wire.saveDraft();
                            }
                        }, 4000);
                        this.$nextTick(() => window.forcePageBot());
                        this.setupSwipeBack();
                    },

                    setViewportHeight() {
                        const root = this.$root;
                        if (!root) return;
                        const vv = window.visualViewport;
                        const viewportHeight = vv ? vv.height : window.innerHeight;
                        const shell = root.querySelector('.ptp-shell');
                        if (!shell) return;
                        // Measure the shell's top position in the current viewport.
                        // If it's partially scrolled off (negative), treat as 0 so
                        // the shell fills the whole viewport and only the messages
                        // area scrolls internally.
                        const rect = shell.getBoundingClientRect();
                        const top = Math.max(0, rect.top);
                        const available = Math.max(360, viewportHeight - top - 20);
                        shell.style.height = available + 'px';
                    },

                    recomputeTier() {
                        const w = window.innerWidth;
                        const next = w < 768 ? 'mobile' : (w < 1200 ? 'tablet' : 'desktop');
                        if (next === this.tier) return;
                        const wasMobile = this.tier === 'mobile';
                        this.tier = next;
                        if (next === 'mobile') {
                            this.activePanel = this.activeFriendId ? 'chat' : 'contacts';
                        } else if (wasMobile) {
                            this.activePanel = 'contacts';
                        }
                    },

                    setupSwipeBack() {
                        const root = this.$root;
                        if (!root) return;
                        const self = this;
                        root.addEventListener('touchstart', (e) => {
                            if (self.tier !== 'mobile' || self.activePanel !== 'chat') return;
                            self._swipeStartX = e.touches[0].clientX;
                            self._swipeStartY = e.touches[0].clientY;
                        }, { passive: true });
                        root.addEventListener('touchend', (e) => {
                            if (self.tier !== 'mobile' || self.activePanel !== 'chat') return;
                            const dx = e.changedTouches[0].clientX - self._swipeStartX;
                            const dy = e.changedTouches[0].clientY - self._swipeStartY;
                            if (self._swipeStartX < 40 && dx > 60 && Math.abs(dy) < 50) {
                                self.onBack();
                            }
                        }, { passive: true });
                    },

                    onBack() {
                        this.searchOpen = false;
                        this.searchQuery = '';
                        this.$wire.set('searchQuery', '');
                        this.activeFriendId = null;
                        if (this.tier === 'mobile') this.activePanel = 'contacts';
                        this.$wire.goBack();
                    },

                    /* ═════════════════════════════════════════════════
                       IN-CONVERSATION SEARCH — real time
                    ═════════════════════════════════════════════════ */
                    openSearch() {
                        this.searchOpen = true;
                    },

                    liveSearch() {
                        // Push the current Alpine input into Livewire.
                        // Debounce is handled by Alpine's @input.debounce.
                        this.$wire.set('searchQuery', this.searchQuery ?? '');
                    },

                    // Kept as an alias in case anything still references it
                    commitSearch() { this.liveSearch(); },

                    closeSearch() {
                        this.searchQuery = '';
                        this.searchOpen = false;
                        this.$wire.set('searchQuery', '');
                    },

                    toggleStarred() {
                        this.showStarredOnly = !this.showStarredOnly;
                        this.$wire.set('showStarredOnly', this.showStarredOnly);
                    },

                    isVisible(id, nameLower) {
                        if (!this.search) return true;
                        return nameLower.includes(this.search.toLowerCase());
                    },
                    filterFriends() {
                        var s = this.search.toLowerCase();
                        if (!s) { this.filteredCount = this.friendIds.length; return; }
                        this.filteredCount = Object.values(this.friendNames)
                            .filter(n => n.toLowerCase().includes(s)).length;
                    },
                    selectFriend(id) {
                        if (this.switchingFriend) return;
                        this.switchingFriend = true;
                        Promise.resolve(this.$wire.setActiveFriend(id))
                            .then(() => {
                                this.activeFriendId = id;
                                this.searchOpen = false;
                                this.searchQuery = '';
                                this.$wire.set('searchQuery', '');
                                if (this.tier === 'mobile') this.activePanel = 'chat';
                                if (window.ChatBridge && window.ChatBridge.subscribeToChat) {
                                    window.ChatBridge.subscribeToChat(id);
                                }
                                this.$nextTick(() => {
                                    this.setViewportHeight();
                                    window.forcePageBot();
                                });
                            })
                            .catch((e) => { console.error('[Chat] Failed to open conversation:', e); })
                            .finally(() => { this.switchingFriend = false; });
                    },

                    onSend() {
                        this.showEmoji = false;
                        this.showAttachMenu = false;
                        this.showStickers = false;
                        window.forcePageBot();
                    },
                    addEmoji(e) { this.messageText += e.detail.unicode; },
                    startTyping() {
                        if (!this.isTyping) {
                            this.isTyping = true;
                            this.$wire.call('startTyping');
                            setTimeout(() => { this.isTyping = false; }, 5000);
                        } else {
                            this.$wire.call('startTyping');
                        }
                    },
                    handleKeydown(event) {
                        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'f') {
                            event.preventDefault(); this.openSearch(); return;
                        }
                        if (event.key === 'Escape') {
                            if (this.searchOpen) { this.closeSearch(); return; }
                            this.$wire.cancelReply(); this.$wire.cancelEdit(); return;
                        }
                        this.startTyping();
                        if (event.key === 'Enter' && !event.shiftKey) {
                            event.preventDefault();
                            this.$wire.sendMessage();
                            this.showEmoji = false; this.showAttachMenu = false;
                            window.forcePageBot();
                        }
                    },
                    startCall(type, friendId, friendName, friendAvatar) {
                        if (window.CallManager) window.CallManager.startCall(type, friendId, friendName, friendAvatar);
                    },
                    openContextMenu(e, msgId, isMine, isDeleted) {
                        e.stopPropagation();
                        var menu = document.getElementById('ctx-menu');
                        if (!menu) return;
                        if (window.Alpine) {
                            var data = Alpine.$data(menu);
                            if (data) { data.msgId = msgId; data.isMine = isMine; data.isDeleted = isDeleted; }
                        }
                        var rect = e.currentTarget.getBoundingClientRect();
                        var top = Math.min(window.innerHeight - 380, rect.bottom + 4);
                        var left = Math.min(window.innerWidth - 220, rect.left);
                        menu.style.top = Math.max(8, top) + 'px';
                        menu.style.left = Math.max(8, left) + 'px';
                        menu.classList.add('show');
                    },
                    quickReact(e, msgId) { this.openContextMenu(e, msgId, false, false); },
                    handleDrop(ev) {
                        const file = ev.dataTransfer.files?.[0];
                        if (!file) return;
                        this.uploadFile(file);
                    },
                    handlePaste(ev) {
                        const items = ev.clipboardData?.items || [];
                        for (const item of items) {
                            if (item.type.startsWith('image/')) {
                                const file = item.getAsFile();
                                if (file) {
                                    this.uploadFile(new File([file], `pasted_${Date.now()}.png`, { type: file.type }));
                                    break;
                                }
                            }
                        }
                    },
                    uploadFile(file) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        let input = document.querySelector('input[type=file][wire\\:model="attachment"]');
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'file';
                            input.setAttribute('wire:model', 'attachment');
                            input.style.display = 'none';
                            document.body.appendChild(input);
                            if (window.Livewire && window.Livewire.rescan) window.Livewire.rescan();
                        }
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    },
                    async toggleRecording() {
                        if (this.recording) { this.stopRecording(); return; }
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                            this.mediaRecorder = new MediaRecorder(stream);
                            this.audioChunks = [];
                            this.recordingStart = Date.now();
                            this.recording = true;
                            this.recordingTimeFormatted = '00:00';
                            this.timerInterval = setInterval(() => {
                                const s = Math.floor((Date.now() - this.recordingStart) / 1000);
                                const m = String(Math.floor(s / 60)).padStart(2, '0');
                                const ss = String(s % 60).padStart(2, '0');
                                this.recordingTimeFormatted = `${m}:${ss}`;
                            }, 500);
                            this.mediaRecorder.ondataavailable = e => {
                                if (e.data.size) this.audioChunks.push(e.data);
                            };
                            this.mediaRecorder.onstop = () => {
                                const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                                const reader = new FileReader();
                                reader.onloadend = () => {
                                    const duration = Math.floor((Date.now() - this.recordingStart) / 1000);
                                    this.$wire.sendVoiceNote(reader.result, duration);
                                };
                                reader.readAsDataURL(blob);
                                stream.getTracks().forEach(t => t.stop());
                            };
                            this.mediaRecorder.start();
                        } catch (e) {
                            alert('Microphone permission required.');
                            this.recording = false;
                        }
                    },
                    stopRecording() {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                        this.recording = false;
                        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') this.mediaRecorder.stop();
                    },
                    cancelRecording() {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                        this.recording = false;
                        if (this.mediaRecorder) {
                            this.mediaRecorder.onstop = null;
                            if (this.mediaRecorder.state !== 'inactive') this.mediaRecorder.stop();
                            this.mediaRecorder.stream?.getTracks().forEach(t => t.stop());
                        }
                    },
                };
            };

            window.ctxMenuState = function ctxMenuState() {
                return {
                    msgId: null, isMine: false, isDeleted: false,
                    react(emoji) {
                        if (this.msgId) this.$wire.toggleReaction(this.msgId, emoji);
                        this.close();
                    },
                    doAction(action) {
                        const id = this.msgId;
                        if (!id) return;
                        switch (action) {
                            case 'reply': this.$wire.setReply(id); break;
                            case 'edit': this.$wire.setEdit(id); break;
                            case 'forward': this.$wire.startForward(id); break;
                            case 'copy': this.$wire.copyMessage(id); break;
                            case 'star': this.$wire.toggleStar(id); break;
                            case 'pin': this.$wire.togglePin(id); break;
                            case 'deleteMe': this.$wire.deleteForMe(id); break;
                            case 'deleteAll': this.$wire.deleteForEveryone(id); break;
                        }
                        this.close();
                    },
                    close() { document.getElementById('ctx-menu')?.classList.remove('show'); },
                };
            };

            document.addEventListener('alpine:init', () => {
                if (!Alpine.store('chat')) Alpine.store('chat', { open: true });
            });

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({ el }) => {
                    if (el && el.querySelectorAll && window.bootstrap?.Dropdown) {
                        el.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (toggle) {
                            bootstrap.Dropdown.getOrCreateInstance(toggle);
                        });
                    }
                });
            });

            window.addEventListener('unread-count-updated', function (e) {
                const badge = document.getElementById('dz-msg-unread-badge');
                if (!badge) return;
                const count = e.detail?.count ?? 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            });

            window.forcePageBot = function () {
                var el = document.getElementById('DZ_Page_Messages_Body');
                if (el) el.scrollTop = el.scrollHeight;
            };

            window.addEventListener('scroll-to-bottom', function () { window.forcePageBot(); });

            window.addEventListener('clear-input', function () {
                var el = document.querySelector('[x-model="messageText"]');
                if (el) el.value = '';
            });

            document.addEventListener('livewire:initialized', function () {
                var initialFriendId = @json($activeFriendId);
                if (initialFriendId && window.ChatBridge) {
                    window.ChatBridge.subscribeToChat(initialFriendId);
                }
            });
        </script>
    @endpush
</div>