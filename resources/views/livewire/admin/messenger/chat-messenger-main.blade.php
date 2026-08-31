<div x-data="pageChatState(@js($friendIds), @js($friends->pluck('name', 'id')->toArray()))" x-init="init()"
    wire:ignore.self>

    {{-- ═══ Styles ═══ --}}
    <style>
        /* ----- Friend list ----- */
        .people-list .chat-p {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer !important;
            transition: background .15s;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .people-list .chat-p:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        .people-list .chat-p .d-flex {
            min-width: 0;
            flex: 1 1 auto;
            pointer-events: none;
            /* allow parent click */
        }

        .people-list .chat-p .ms-2 {
            min-width: 0;
            flex: 1 1 auto;
        }

        .people-list .chat-p .ms-2 h6 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            margin-bottom: 2px;
        }

        .dz-last-msg {
            margin: 0;
            font-size: .8rem;
            color: #8a8a8a;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dz-last-msg.dz-unread {
            color: #262626;
            font-weight: 600;
        }

        .dz-unread-pill {
            flex-shrink: 0;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(76, 175, 80, .4);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .chat-p.style-1.dz-active {
            background: rgba(13, 153, 255, 0.08);
        }

        /* ----- Call / header buttons ----- */
        .chat-p .chat-admin .icon-box,
        .chat-p .chat-admin button.icon-box {
            border: none;
            padding: 0;
            margin: 0 4px;
            outline: none;
            box-sizing: border-box;
            line-height: 0;
            flex-shrink: 0;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .chat-p .chat-admin {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .chat-p .chat-admin .dz-chat-history-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            margin: 0 6px 0 0;
            cursor: pointer;
        }

        .chat-p .chat-admin .chat-toggle {
            display: inline-flex !important;
        }

        /* ----- Chat body ----- */
        .chat-box-area {
            height: 60vh;
            min-height: 360px;
            overflow-y: auto;
        }

        /* ----- Input bar ----- */
        .message-send.style-2 {
            position: relative;
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message-send.style-2 .type-massage {
            flex: 1;
        }

        .message-send.style-2 .type-massage .input-group {
            background: #f1f3f5;
            border-radius: 30px;
            padding: 2px 4px 2px 16px;
            align-items: center;
        }

        .message-send.style-2 .type-massage .input-group textarea {
            border: none;
            background: transparent;
            resize: none;
            padding: 8px 0;
            font-size: 0.9rem;
            flex: 1;
            outline: none;
            box-shadow: none;
        }

        .message-send.style-2 .type-massage .input-group .input-group-append {
            display: flex;
            align-items: center;
            gap: 4px;
            padding-right: 4px;
        }

        .message-send.style-2 .type-massage .input-group .input-group-append .btn-send {
            border-radius: 50px;
            padding: 8px 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0d99ff;
            color: #fff;
            border: none;
            transition: background .2s;
            cursor: pointer;
        }

        .message-send.style-2 .type-massage .input-group .input-group-append .btn-send:hover {
            background: #0a7acc;
        }

        .message-send.style-2 .left-actions .btn {
            border: 1px solid #dee2e6;
            background: transparent;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #6c757d;
            transition: all .2s;
            cursor: pointer;
        }

        .message-send.style-2 .left-actions .btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
            color: #333;
        }

        /* ----- Popups (emoji / attach / sticker) ----- */
        .emoji-picker-container,
        .attach-menu,
        .sticker-grid {
            position: absolute;
            bottom: 70px;
            left: 0;
            z-index: 999;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .emoji-picker-container.show,
        .attach-menu.show,
        .sticker-grid.show {
            display: block;
        }

        .emoji-picker-container {
            padding: 4px;
            width: 340px;
        }

        .attach-menu {
            padding: 12px;
            display: none;
            flex-wrap: wrap;
            gap: 8px;
            width: 220px;
        }

        .attach-menu.show {
            display: flex;
        }

        .attach-menu .btn {
            border-radius: 8px;
            width: auto;
            padding: 6px 12px;
            font-size: 0.75rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            cursor: pointer;
        }

        .attach-menu .btn:hover {
            background: #e9ecef;
        }

        .sticker-grid {
            padding: 12px;
            display: none;
            flex-wrap: wrap;
            gap: 4px;
            width: 220px;
            max-height: 200px;
            overflow-y: auto;
        }

        .sticker-grid.show {
            display: flex;
        }

        .sticker-grid .btn {
            border-radius: 8px;
            width: auto;
            padding: 4px 8px;
            font-size: 1.5rem;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .sticker-grid .btn:hover {
            background: #f1f3f5;
        }

        /* ----- Media / Files ----- */
        .chat-meadia .image-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .chat-meadia .image-list img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform .2s;
        }

        .chat-meadia .image-list img:hover {
            transform: scale(1.03);
        }

        .chat-meadia .file-list .filie-l-icon {
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            background: #f8f9fc;
            margin-bottom: 10px;
            transition: background .2s;
            cursor: pointer;
        }

        .chat-meadia .file-list .filie-l-icon:hover {
            background: #eef1f5;
        }

        .chat-meadia .file-list .filie-l-icon img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .chat-meadia .file-list .filie-l-icon h5 {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-meadia .file-list .filie-l-icon span {
            font-size: 0.65rem;
            color: #999;
        }

        /* ----- General tweaks ----- */
        .input-group-text {
            background: transparent;
            border: none;
        }

        .input-group-text a {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .people-list .chat-p .d-flex.active .avatar {
            border: 2px solid var(--primary);
        }

        .people-list .chat-p .d-flex.active .ms-2 h6 {
            color: var(--primary);
        }

        .online_icon {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #fff;
            position: absolute;
            bottom: 2px;
            right: 2px;
        }

        .online_icon.offline {
            background: #d0d0d0;
        }

        .online_icon:not(.offline) {
            background: #3AC977;
        }

        /* Ensure all buttons have pointer cursor */
        button,
        a,
        .btn,
        [role="button"] {
            cursor: pointer;
        }
    </style>

    <div class="container-fluid">
        <div class="row gx-0">
            <div class="col-xl-12">
                <div class="card overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row gx-0">

                            {{-- ═══ CONTACTS (LEFT) ═══ --}}
                            <div class="col-xl-3 col-lg-6 col-sm-5 chat-border mobile-chat chat-left-area">
                                <div class="chat-p shaprate">
                                    <div class="d-flex">
                                        <img src="{{ auth()->user()->getAvatarUrlAttribute() }}"
                                            class="avatar avatar-md rounded-circle" alt="">
                                        <div class="ms-2">
                                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                            <span>{{ $totalReceivedMessages }} unread</span>
                                        </div>
                                    </div>
                                    <div class="icon-box bg-primary-light">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M17.3389 6.35305L16.8202 5.45298C16.3814 4.69138 15.4089 4.42864 14.6463 4.86564V4.86564C14.2832 5.07949 13.85 5.14017 13.4422 5.03428C13.0344 4.92839 12.6855 4.66464 12.4723 4.30118C12.3352 4.07016 12.2616 3.80704 12.2588 3.53841V3.53841C12.2711 3.10773 12.1087 2.69038 11.8083 2.38143C11.508 2.07249 11.0954 1.89826 10.6646 1.89844H9.61956C9.19745 1.89843 8.79274 2.06664 8.49498 2.36583C8.19722 2.66502 8.03096 3.07053 8.03299 3.49264V3.49264C8.02048 4.36415 7.31038 5.06405 6.43879 5.06396C6.17016 5.06117 5.90703 4.98749 5.67601 4.85038V4.85038C4.91336 4.41339 3.94091 4.67612 3.5021 5.43772L2.94527 6.35305C2.50699 7.1137 2.76615 8.08555 3.52498 8.52697V8.52697C4.01823 8.81174 4.32209 9.33803 4.32209 9.90759C4.32209 10.4771 4.01823 11.0034 3.52498 11.2882V11.2882C2.76711 11.7267 2.50767 12.6961 2.94527 13.4545V13.4545L3.47158 14.3622C3.67719 14.7332 4.02215 15.007 4.43014 15.1229C4.83813 15.2389 5.27551 15.1875 5.6455 14.9801V14.9801C6.00921 14.7678 6.44264 14.7097 6.84943 14.8185C7.25622 14.9274 7.60268 15.1942 7.81178 15.5598C7.94889 15.7908 8.02257 16.0539 8.02536 16.3225V16.3225C8.02536 17.203 8.73911 17.9167 9.61956 17.9167H10.6646C11.5421 17.9168 12.2546 17.2076 12.2588 16.3302V16.3302C12.2567 15.9067 12.424 15.5001 12.7234 15.2006C13.0229 14.9012 13.4295 14.7339 13.853 14.736C14.121 14.7431 14.383 14.8165 14.6157 14.9495V14.9495C15.3764 15.3878 16.3482 15.1287 16.7897 14.3698V14.3698L17.3389 13.4545C17.5514 13.0896 17.6098 12.655 17.501 12.247C17.3922 11.839 17.1252 11.4912 16.7592 11.2806V11.2806C16.3931 11.07 16.1261 10.7222 16.0173 10.3142C15.9085 9.90613 15.9669 9.47156 16.1794 9.10668C16.3177 8.86532 16.5178 8.66521 16.7592 8.52697V8.52697C17.5134 8.08579 17.772 7.11962 17.3389 6.36068V6.36068V6.35305Z"
                                                stroke="var(--primary)" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <ellipse cx="10.1459" cy="9.90749" rx="2.1968" ry="2.1968"
                                                stroke="var(--primary)" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="c-list">
                                    <div class="input-group search-area">
                                        <input type="text" x-model="search" @input="filterFriends()"
                                            class="form-control" placeholder="Search">
                                        <span class="input-group-text">
                                            <a href="javascript:void(0)">
                                                <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="8.82495" cy="9.32491" r="6.74142" stroke="#0D99FF"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M13.5137 14.3638L16.1568 16.9999" stroke="#0D99FF"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>

                                <div class="people-list dz-scroll">
                                    @foreach($friends as $friend)
                                        <div class="chat-p style-1 {{ $activeFriendId === $friend->id ? 'dz-active' : '' }}"
                                            wire:key="page-friend-{{ $friend->id }}"
                                            x-show="isVisible({{ $friend->id }}, '{{ addslashes(strtolower($friend->name)) }}')"
                                            @click="$wire.isLoading ? null : $wire.setActiveFriend({{ $friend->id }})"
                                            :class="{ 'opacity-50': $wire.isLoading }">
                                            <div class="d-flex {{ $activeFriendId === $friend->id ? 'active' : '' }}">
                                                <div class="position-relative">
                                                    <img src="{{ $friend->getAvatarUrlAttribute() }}"
                                                        class="avatar avatar-md rounded-circle" alt="">
                                                    <span class="online_icon {{ $friend->online ? '' : 'offline' }}"></span>
                                                </div>
                                                <div class="ms-2">
                                                    <h6 class="mb-0">{{ $friend->name }}</h6>
                                                    <span
                                                        class="dz-last-msg {{ $friend->message_count > 0 ? 'dz-unread' : '' }}">
                                                        @if($friend->last_message_type === 'image') 📷 Photo
                                                        @elseif($friend->last_message_type === 'document') 📄 Document
                                                        @elseif($friend->last_message_type === 'audio') 🎵 Audio
                                                        @elseif($friend->last_message_type === 'video') 🎬 Video
                                                        @elseif($friend->last_message_type === 'sticker') Sticker
                                                        @elseif($friend->last_message_body)
                                                            <strong>{{ $friend->last_message_is_mine ? 'You: ' : '' }}</strong>{{ Str::limit($friend->last_message_body, 30) }}
                                                        @else
                                                            {{ $friend->online ? 'Active now' : 'Last seen ' . $friend->lastSeenForHumans() }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            @if($friend->message_count > 0)
                                                <span
                                                    class="dz-unread-pill">{{ $friend->message_count > 99 ? '99+' : $friend->message_count }}</span>
                                            @else
                                                <span>{{ $friend->last_message_time?->diffForHumans(null, true) }}</span>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if($friends->isEmpty())
                                        <p class="text-center text-muted p-4">No conversations yet</p>
                                    @endif
                                </div>
                            </div>

                            {{-- ═══ CONVERSATION (MIDDLE) ═══ --}}
                            <div class="col-xl-5 col-lg-6 col-sm-7 chat-border">
                                @php $selectedFriend = $activeFriendId ? $friends->firstWhere('id', $activeFriendId) : null; @endphp

                                @if($selectedFriend)
                                    <div wire:key="page-chat-{{ $activeFriendId }}">
                                        {{-- Header --}}
                                        <div class="chat-p shaprate">
                                            <div class="d-flex">
                                                <img src="{{ $selectedFriend->getAvatarUrlAttribute() }}"
                                                    class="avatar avatar-md rounded-circle" alt="">
                                                <div class="ms-2">
                                                    <h6 class="mb-0">{{ $selectedFriend->name }}</h6>
                                                    <span>
                                                        @if($selectedFriend->online)
                                                            @if($this->isFriendTyping())
                                                                <span class="text-primary">Typing…</span>
                                                            @else
                                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <circle cx="7" cy="7" r="6" fill="#3AC977" stroke="white"
                                                                        stroke-width="2" />
                                                                </svg>
                                                                online
                                                            @endif
                                                        @else
                                                            Last seen {{ $selectedFriend->lastSeenForHumans() }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="chat-admin">
                                                {{-- Back to contacts --}}
                                                <a href="javascript:void(0);" class="dz-chat-history-back chat-toggle"
                                                    wire:click="goBack" title="Back" style="flex-shrink: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                        height="18px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                            <rect fill="#000000" opacity="0.3"
                                                                transform="translate(15.000000, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-15.000000, -12.000000) "
                                                                x="14" y="7" width="2" height="10" rx="1" />
                                                            <path
                                                                d="M3.7071045,15.7071045 C3.3165802,16.0976288 2.68341522,16.0976288 2.29289093,15.7071045 C1.90236664,15.3165802 1.90236664,14.6834152 2.29289093,14.2928909 L8.29289093,8.29289093 C8.67146987,7.914312 9.28105631,7.90106637 9.67572234,8.26284357 L15.6757223,13.7628436 C16.0828413,14.136036 16.1103443,14.7686034 15.7371519,15.1757223 C15.3639594,15.5828413 14.7313921,15.6103443 14.3242731,15.2371519 L9.03007346,10.3841355 L3.7071045,15.7071045 Z"
                                                                fill="#000000" fill-rule="nonzero"
                                                                transform="translate(9.000001, 11.999997) scale(-1, -1) rotate(90.000000) translate(-9.000001, -11.999997) " />
                                                        </g>
                                                    </svg>
                                                </a>
                                                {{-- Audio call --}}
                                                <button type="button" class="icon-box bg-success mx-1 border-0"
                                                    title="Audio call"
                                                    @click="startCall('audio', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M18.3333 14.0999V16.5999C18.3343 16.832 18.2867 17.0617 18.1937 17.2744C18.1008 17.487 17.9644 17.6779 17.7934 17.8348C17.6224 17.9917 17.4205 18.1112 17.2006 18.1855C16.9808 18.2599 16.7478 18.2875 16.5167 18.2666C13.9523 17.988 11.4892 17.1117 9.32498 15.7083C7.31151 14.4288 5.60443 12.7217 4.32499 10.7083C2.91663 8.53426 2.04019 6.05908 1.76665 3.48325C1.74583 3.25281 1.77321 3.02055 1.84707 2.80127C1.92092 2.58199 2.03963 2.38049 2.19562 2.2096C2.35162 2.03871 2.54149 1.90218 2.75314 1.80869C2.9648 1.7152 3.1936 1.6668 3.42499 1.66658H5.92499C6.32941 1.6626 6.72148 1.80582 7.02812 2.06953C7.33476 2.33324 7.53505 2.69946 7.59165 3.09992C7.69717 3.89997 7.89286 4.68552 8.17499 5.44158C8.2871 5.73985 8.31137 6.06401 8.24491 6.37565C8.17844 6.68729 8.02404 6.97334 7.79998 7.19992L6.74165 8.25825C7.92795 10.3445 9.65536 12.072 11.7417 13.2583L12.8 12.1999C13.0266 11.9759 13.3126 11.8215 13.6243 11.755C13.9359 11.6885 14.26 11.7128 14.5583 11.8249C15.3144 12.107 16.0999 12.3027 16.9 12.4083C17.3048 12.4654 17.6745 12.6693 17.9388 12.9812C18.203 13.2931 18.3435 13.6912 18.3333 14.0999Z"
                                                            stroke="white" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                                {{-- Video call --}}
                                                <button type="button" class="icon-box bg-primary mx-1 border-0"
                                                    title="Video call"
                                                    @click="startCall('video', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                                    <svg width="20" height="14" viewBox="0 0 20 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M19.561 1.172C19.4256 1.08045 19.2699 1.02347 19.1074 1.00604C18.945 0.988603 18.7807 1.01125 18.629 1.072L14.954 2.542C14.8449 1.83596 14.4875 1.19201 13.946 0.726018C13.4045 0.260026 12.7144 0.00258053 12 0H3C2.20435 0 1.44129 0.316071 0.87868 0.87868C0.316071 1.44129 0 2.20435 0 3V11C0 11.7956 0.316071 12.5587 0.87868 13.1213C1.44129 13.6839 2.20435 14 3 14H12C12.7143 13.9975 13.4042 13.7402 13.9457 13.2744C14.4872 12.8086 14.8447 12.1649 14.954 11.459L18.629 12.929C18.7807 12.9896 18.945 13.0121 19.1075 12.9946C19.27 12.977 19.4257 12.9199 19.561 12.8282C19.6962 12.7365 19.807 12.6131 19.8835 12.4687C19.9601 12.3244 20.0001 12.1634 20 12V2C20 1.83663 19.96 1.67573 19.8835 1.53139C19.807 1.38705 19.6962 1.26365 19.561 1.172ZM12 12H3C2.73478 12 2.48043 11.8946 2.29289 11.7071C2.10536 11.5196 2 11.2652 2 11V3C2 2.73478 2.10536 2.48043 2.29289 2.29289C2.48043 2.10536 2.73478 2 3 2H12C12.2652 2 12.5196 2.10536 12.7071 2.29289C12.8946 2.48043 13 2.73478 13 3V11C13 11.2652 12.8946 11.5196 12.7071 11.7071C12.5196 11.8946 12.2652 12 12 12ZM18 10.523L15 9.323V4.677L18 3.477V10.523Z"
                                                            fill="white" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Messages --}}
                                        <div class="chat-box-area style-2 dz-scroll" id="DZ_Page_Messages_Body">
                                            @php $newBadgeInserted = false; @endphp
                                            @foreach($this->messages as $message)
                                                @if(!$newBadgeInserted && $firstUnreadMessageId && $message->id == $firstUnreadMessageId)
                                                    @php $newBadgeInserted = true; @endphp
                                                    <span class="text-center d-block mb-4">New messages</span>
                                                @endif

                                                @php $isMine = $message->sender_id === auth()->id(); @endphp
                                                <div class="media {{ $isMine ? 'justify-content-end align-items-end ms-auto' : '' }}"
                                                    wire:key="page-msg-{{ $message->id }}">
                                                    @if(!$isMine)
                                                        <img src="{{ $selectedFriend->getAvatarUrlAttribute() }}"
                                                            class="avatar rounded-circle" style="width:34px;height:34px;" alt="">
                                                    @endif
                                                    <div class="{{ $isMine ? 'message-sent' : 'message-received' }} w-auto">
                                                        @if($message->attachment_type === 'sticker')
                                                            <span style="font-size:2.5rem;">{{ $message->attachment_path }}</span>
                                                        @elseif($message->attachment_type === 'image')
                                                            <a href="{{ asset('storage/' . $message->attachment_path) }}"
                                                                target="_blank"><img
                                                                    src="{{ asset('storage/' . $message->attachment_path) }}"
                                                                    style="max-width:220px;border-radius:8px;display:block;">
                                                            </a>
                                                            @if($message->body)
                                                            <p class="mb-0">{{ $message->body }}</p>@endif
                                                        @elseif($message->attachment_type === 'video')
                                                            <video controls style="max-width:220px;border-radius:8px;">
                                                                <source src="{{ asset('storage/' . $message->attachment_path) }}">
                                                            </video>
                                                            @if($message->body)
                                                            <p class="mb-0">{{ $message->body }}</p>@endif
                                                        @elseif($message->attachment_type === 'audio')
                                                            <audio controls style="width:100%;">
                                                                <source src="{{ asset('storage/' . $message->attachment_path) }}">
                                                            </audio>
                                                        @elseif($message->attachment_type === 'document')
                                                            <a href="{{ asset('storage/' . $message->attachment_path) }}"
                                                                target="_blank" class="d-block">
                                                                <i class="fa-solid fa-file-pdf"></i>
                                                                {{ $message->attachment_name ?? 'Document' }}
                                                            </a>
                                                        @else
                                                            <p class="mb-1">{{ $message->body }}</p>
                                                        @endif
                                                        <span class="fs-12">
                                                            {{ $message->created_at->format('h:i A') }}
                                                            @if($isMine && $message->read)
                                                                <i class="fa fa-check-circle text-primary"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach

                                            @if($this->messages->isEmpty())
                                                <p class="text-center text-muted mt-4">
                                                    Say hi to {{ $selectedFriend->name }} 👋
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Attachment preview --}}
                                        @if($showAttachmentPreview)
                                            <div class="d-flex align-items-center px-3 py-2 border-top" style="gap:10px;">
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
                                                <button wire:click="clearAttachment" @mousedown.prevent
                                                    class="btn btn-sm btn-light"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        @endif

                                        {{-- ═══ INPUT BAR ═══ --}}
                                        <div class="message-send style-2">
                                            <div class="left-actions d-flex align-items-center gap-1">
                                                <button type="button" class="btn" @mousedown.prevent
                                                    @click="showEmoji = !showEmoji" title="Emoji">
                                                    <i class="fa-regular fa-face-smile"></i>
                                                </button>
                                                <button type="button" class="btn" @mousedown.prevent
                                                    @click="showAttachMenu = !showAttachMenu" title="Attach">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                </button>
                                            </div>
                                            <div class="type-massage style-1 flex-grow-1">
                                                <div class="input-group">
                                                    <textarea rows="1" class="form-control" placeholder="Type a message…"
                                                        x-model="messageText" @keydown="handleKeydown($event)"
                                                        @input="startTyping()"></textarea>
                                                    <div class="input-group-append">
                                                        {{--
                                                        FIX: @mousedown.prevent stops the textarea from
                                                        blurring (and firing the Livewire .live sync +
                                                        DOM morph) before the click event completes.
                                                        Without it, the button element can be replaced
                                                        mid-click by the morph, so the click is silently
                                                        lost and only Enter (handled purely client-side
                                                        in keydown) ever worked.
                                                        --}}
                                                        <button type="button" class="btn btn-send" @mousedown.prevent
                                                            wire:click="sendMessage" @click="onSend()">
                                                            <i class="fa-solid fa-paper-plane"></i> Send
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="emoji-picker-container" :class="{ 'show': showEmoji }"
                                                x-show="showEmoji" @click.away="showEmoji = false">
                                                <emoji-picker @emoji-click="addEmoji($event)"></emoji-picker>
                                            </div>
                                            <div class="attach-menu" :class="{ 'show': showAttachMenu }"
                                                x-show="showAttachMenu" @click.away="showAttachMenu = false">
                                                <label class="btn"><i class="fa-solid fa-image"></i> Photo <input
                                                        type="file" wire:model="attachment" accept="image/*"
                                                        class="d-none"></label>
                                                <label class="btn"><i class="fa-solid fa-video"></i> Video <input
                                                        type="file" wire:model="attachment" accept="video/*"
                                                        class="d-none"></label>
                                                <label class="btn"><i class="fa-solid fa-music"></i> Audio <input
                                                        type="file" wire:model="attachment" accept="audio/*"
                                                        class="d-none"></label>
                                                <label class="btn"><i class="fa-solid fa-file-lines"></i> Document <input
                                                        type="file" wire:model="attachment"
                                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt" class="d-none"></label>
                                                <button class="btn" @mousedown.prevent
                                                    @click="showStickers = !showStickers">
                                                    <i class="fa-regular fa-face-smile"></i> Sticker
                                                </button>
                                            </div>
                                            <div class="sticker-grid" :class="{ 'show': showStickers }"
                                                x-show="showStickers" @click.away="showStickers = false">
                                                @foreach(['😂', '😍', '🥰', '😎', '🤩', '😭', '🙏', '👏', '🔥', '💯', '❤️', '💔', '🎉', '🎊', '✨', '🌟', '😤', '😴', '🤔', '🥳', '🤯', '😱', '👀', '💪', '🙌', '😅', '🫶', '💀', '🤣', '🥹'] as $sticker)
                                                    <button class="btn" @mousedown.prevent
                                                        wire:click="sendSticker('{{ $sticker }}')"
                                                        @click="showStickers = false">{{ $sticker }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-center h-100 text-muted p-5">
                                        <i class="fa-solid fa-comments fa-3x mb-3"></i>
                                        <p class="mb-0">Pick a conversation on the left to start chatting.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ═══ MEDIA & FILES (RIGHT) ═══ --}}
                            <div class="col-xl-4">
                                @if($selectedFriend)
                                    <div class="chat-meadia" wire:key="page-media-{{ $activeFriendId }}">
                                        <h4 class="fs-16">Media</h4>
                                        <div class="image-list">
                                            @forelse($this->mediaMessages as $mediaMessage)
                                                <a href="{{ asset('storage/' . $mediaMessage->attachment_path) }}"
                                                    target="_blank">
                                                    @if($mediaMessage->attachment_type === 'video')
                                                        <video style="width:100%;height:100%;object-fit:cover;">
                                                            <source src="{{ asset('storage/' . $mediaMessage->attachment_path) }}">
                                                        </video>
                                                    @else
                                                        <img src="{{ asset('storage/' . $mediaMessage->attachment_path) }}" alt="">
                                                    @endif
                                                </a>
                                            @empty
                                                <p class="text-muted small mb-0">No photos or videos shared yet.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="chat-meadia">
                                        <h4 class="fs-16">Files</h4>
                                        <div class="file-list row dz-scroll">
                                            @forelse($this->fileMessages as $fileMessage)
                                                <div class="text-center col-xl-4 col-6 filie-l-icon">
                                                    <a href="{{ asset('storage/' . $fileMessage->attachment_path) }}"
                                                        target="_blank">
                                                        <i class="fa-solid fa-file-pdf fa-2x"></i>
                                                        <h5 class="text-truncate">
                                                            {{ $fileMessage->attachment_name ?? 'Document' }}
                                                        </h5>
                                                        <span>{{ $fileMessage->created_at->format('M j, Y') }}</span>
                                                    </a>
                                                </div>
                                            @empty
                                                <p class="text-muted small mb-0 px-2">No files shared yet.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 text-muted small">
                                        Media and files shared in a conversation will show up here.
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Call overlays --}}
    @livewire('admin.messenger.call-overlays')

    {{-- ═══ REALTIME SCRIPTS ═══ --}}
    @push('scripts')
        <script>
            // ── Alpine store and state ──
            document.addEventListener('alpine:init', () => {
                if (!Alpine.store('chat')) {
                    Alpine.store('chat', { open: true });
                }
                window.pageChatState = pageChatState;
            });

            // ── Livewire morph hooks ──
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({ el }) => {
                    if (el && el.querySelectorAll && window.bootstrap?.Dropdown) {
                        el.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (toggle) {
                            bootstrap.Dropdown.getOrCreateInstance(toggle);
                        });
                    }
                });
            });

            // ── Badge updater ──
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

            // ═══════════════════════════════════════════════════════
            // NOTE: All Echo subscription logic now lives ONCE in the
            // shared `window.ChatBridge` object defined in the layout
            // (layouts/users.blade.php). This is the file that was
            // previously BROKEN — it used `getMessengerComponent()`,
            // which grabbed the first Livewire component with a
            // `friends` property, which was virtually always the
            // navbar widget instead of this full-page component. So
            // real-time messages updated the navbar popup but never
            // this page, forcing a manual reload. That's fixed now:
            // ChatBridge dispatches a GLOBAL `message-received` event,
            // and THIS component (ChatMessengerMain) listens for it
            // directly via #[On('message-received')] in its PHP class.
            // ═══════════════════════════════════════════════════════

            // ── Alpine component ──
            function pageChatState(initialFriendIds, friendNames) {
                return {
                    showEmoji: false,
                    showAttachMenu: false,
                    showStickers: false,
                    isTyping: false,
                    messageText: @entangle('messageText').live,
                    friendIds: initialFriendIds,
                    friendNames: friendNames,
                    search: '',
                    filteredCount: initialFriendIds.length,

                    init() {
                        window.ChatBridge.subscribeToProfileUpdates(this.friendIds);

                        const self = this;
                        this.$watch('friendIds', function (newIds) {
                            window.ChatBridge.subscribeToProfileUpdates(newIds);
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
                                window.ChatBridge.subscribeToChat(e.detail.friendId);
                            }
                        });

                        this.$nextTick(() => window.forcePageBot());
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
                    onSend() {
                        this.showEmoji = false;
                        this.showAttachMenu = false;
                        this.showStickers = false;
                        window.forcePageBot();
                    },
                    addEmoji(e) {
                        this.messageText += e.detail.unicode;
                    },
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
                        this.startTyping();
                        if (event.key === 'Enter' && !event.shiftKey) {
                            event.preventDefault();
                            this.$wire.sendMessage();
                            this.showEmoji = false;
                            this.showAttachMenu = false;
                            window.forcePageBot();
                        }
                    },
                    startCall(type, friendId, friendName, friendAvatar) {
                        window.CallManager.startCall(type, friendId, friendName, friendAvatar);
                    },
                };
            }

            window.forcePageBot = function () {
                var el = document.getElementById('DZ_Page_Messages_Body');
                if (el) el.scrollTop = el.scrollHeight;
            };

            window.addEventListener('scroll-to-bottom', function () {
                window.forcePageBot();
            });

            window.addEventListener('clear-input', function () {
                var el = document.querySelector('[x-model="messageText"]');
                if (el) el.value = '';
            });


            document.addEventListener('livewire:initialized', function () {
                var initialFriendId = @json($activeFriendId);
                if (initialFriendId) {
                    window.ChatBridge.subscribeToChat(initialFriendId);
                }
            });
        </script>
    @endpush
</div>