<div x-data="chatState(@js($friendIds), @js(($friends ?? collect())->pluck('name', 'id')->toArray()))" x-init="init()"
    wire:ignore.self>

    {{-- ═══ Styles ═══ --}}
    <style>
        .dz-last-msg {
            margin: 0;
            font-size: .8rem;
            color: #8a8a8a;
            max-width: 148px;
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
            margin-left: 8px;
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

        .call-actions {
            background: #f8f9fa;
            padding: 6px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .call-btn {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 6px 16px;
            border-radius: 50px;
            transition: all .2s;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .call-btn:hover {
            background: #f1f3f5;
            border-color: #adb5bd;
        }

        .call-btn.audio:hover {
            color: #198754;
            border-color: #198754;
            background: rgba(25, 135, 84, 0.08);
        }

        .call-btn.video:hover {
            color: #dc3545;
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.08);
        }

        .call-label {
            font-size: 12px;
            font-weight: 500;
        }
    </style>

    {{--
    FIX: added :class="{ active: $store.chat.open }" alongside the existing
    x-show. The theme's CSS toggles visibility off the `.active` class
    (opacity/visibility transitions), while x-show only toggles inline
    `display`. Previously only a stale, non-delegated jQuery handler in
    w3crm.js (`handleChatbox()`) added/removed `.active`, and that handler
    died on every wire:navigate because it was bound directly to the old
    `.bell-link` node instead of delegated. Now Alpine is the single
    source of truth for both `display` and `.active`, so this persisted
    widget reacts correctly regardless of navigation state.
    --}}
    <div class="chatbox" :class="{ active: $store.chat.open }" x-show="$store.chat.open" wire:ignore.self
        x-transition:enter.duration.300ms>
        <div class="chatbox-close" @click="$store.chat.open = false"></div>

        <div class="custom-tab-1">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#notes">Notes</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#alerts">Alerts</a></li>
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#chat">Chat</a></li>
            </ul>
            <div class="tab-content">
                {{-- ═══ CHAT TAB ═══ --}}
                <div class="tab-pane fade active show" id="chat">
                    {{-- FRIEND LIST --}}
                    @if(!$activeFriendId)
                        <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box" wire:key="friend-list-view">
                            <div class="card-header chat-list-header text-center">
                                <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                        viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                            <rect fill="#000000" opacity="1.0"
                                                transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                x="4" y="11" width="16" height="2" rx="1" />
                                        </g>
                                    </svg></a>
                                <div>
                                    <h6 class="mb-1">Chat List</h6>
                                    <p class="mb-0">Show All</p>
                                </div>
                                <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                        viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <circle fill="#000000" cx="5" cy="12" r="2" />
                                            <circle fill="#000000" cx="12" cy="12" r="2" />
                                            <circle fill="#000000" cx="19" cy="12" r="2" />
                                        </g>
                                    </svg></a>
                            </div>
                            <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body">
                                <div class="p-2">
                                    <div class="position-relative">
                                        <i class="fa fa-search position-absolute"
                                            style="left:10px; top:50%; transform:translateY(-50%); color:#aaa;"></i>
                                        <input type="text" x-model="search" @input="filterFriends()"
                                            placeholder="Search friends..." class="form-control form-control-sm"
                                            style="padding-left:30px; border-radius:20px;">
                                    </div>
                                </div>
                                <ul class="contacts">
                                    @php $currentLetter = null; @endphp
                                    @foreach($friends as $friend)
                                        @php $firstLetter = strtoupper(substr($friend->name, 0, 1)); @endphp
                                        @if($currentLetter !== $firstLetter)
                                            @php $currentLetter = $firstLetter; @endphp
                                            <li class="name-first-letter">{{ $currentLetter }}</li>
                                        @endif
                                        <li x-show="isVisible({{ $friend->id }}, '{{ addslashes(strtolower($friend->name)) }}')"
                                            wire:key="friend-{{ $friend->id }}"
                                            @click.stop="$wire.isLoading ? null : $wire.setActiveFriend({{ $friend->id }})"
                                            :class="{ 'opacity-50': $wire.isLoading }" class="dz-chat-user"
                                            style="cursor: pointer;">
                                            <div class="d-flex bd-highlight">
                                                <div class="img_cont">
                                                    <img src="{{ $friend->getAvatarUrlAttribute() }}"
                                                        class="rounded-circle user_img" alt="">
                                                    <span class="online_icon {{ $friend->online ? '' : 'offline' }}"></span>
                                                </div>
                                                <div class="user_info">
                                                    <span>{{ $friend->name }}</span>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <p
                                                            class="dz-last-msg {{ $friend->message_count > 0 ? 'dz-unread' : '' }}">
                                                            @if($friend->last_message_type === 'image') 📷 Photo
                                                            @elseif($friend->last_message_type === 'document') 📄 Document
                                                            @elseif($friend->last_message_type === 'audio') 🎵 Audio
                                                            @elseif($friend->last_message_type === 'video') 🎬 Video
                                                            @elseif($friend->last_message_type === 'sticker') Sticker
                                                            @elseif($friend->last_message_body)
                                                                {{ Str::limit($friend->last_message_body, 28) }}
                                                            @else
                                                                {{ $friend->online ? 'Active now' : 'Last seen ' . $friend->lastSeenForHumans() }}
                                                            @endif
                                                        </p>
                                                        @if($friend->message_count > 0)
                                                            <span
                                                                class="dz-unread-pill">{{ $friend->message_count > 99 ? '99+' : $friend->message_count }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                    @if($friends->isEmpty())
                                        <li class="text-center text-muted p-4">No users found</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @else
                        {{-- CHAT HISTORY – only if selected friend exists --}}
                        @php $selectedFriend = $friends->firstWhere('id', $activeFriendId); @endphp

                        @if($selectedFriend)
                            <div class="card chat dz-chat-history-box" style="display: block;"
                                wire:key="chat-view-{{ $activeFriendId }}">
                                {{-- ═══ HEADER (row 1) ═══ --}}
                                <div class="card-header chat-list-header text-center d-flex align-items-center justify-content-between"
                                    style="padding: 8px 16px;">
                                    {{-- Back arrow (left) --}}
                                    <a href="javascript:void(0);" class="dz-chat-history-back" wire:click="goBack"
                                        style="flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
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

                                    {{-- User info: name + status (centered) --}}
                                    <div class="flex-grow-1 text-center" style="margin: 0 8px;">
                                        <h6 class="mb-0">Chat with {{ $selectedFriend->name }}</h6>
                                        <p class="mb-0 text-success small">
                                            @if($selectedFriend->online)
                                                @if($this->isFriendTyping())
                                                    <span
                                                        class="typing-indicator">Typing<span>.</span><span>.</span><span>.</span></span>
                                                @else
                                                    Online
                                                @endif
                                            @else
                                                Last seen {{ $selectedFriend->lastSeenForHumans() }}
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Dropdown (right) --}}
                                    <div class="dropdown" style="flex-shrink: 0;">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                                </g>
                                            </svg>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" wire:ignore>
                                            <li class="dropdown-item"><i class="fa fa-user-circle text-primary me-2"></i> View
                                                profile</li>
                                            <li class="dropdown-item"><i class="fa fa-users text-primary me-2"></i> Add to
                                                friends</li>
                                            <li class="dropdown-item"><i class="fa fa-plus text-primary me-2"></i> Add to group
                                            </li>
                                            <li class="dropdown-item"><i class="fa fa-ban text-primary me-2"></i> Block</li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- ═══ CALL ACTIONS (centered row below header) ═══ --}}
                                <div class="call-actions p-2 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        {{-- Audio call --}}
                                        <button class="call-btn audio" title="Audio call"
                                            @click="startCall('audio', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                            <i class="fa fa-phone"></i>
                                            <span class="call-label">Audio</span>
                                        </button>
                                        {{-- Video call --}}
                                        <button class="call-btn video" title="Video call"
                                            @click="startCall('video', {{ $selectedFriend->id }}, '{{ addslashes($selectedFriend->name) }}', '{{ $selectedFriend->getAvatarUrlAttribute() }}')">
                                            <i class="fa fa-video-camera"></i>
                                            <span class="call-label">Video</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- MESSAGES BODY --}}
                                <div class="card-body msg_card_body dz-scroll" id="DZ_W_Contacts_Body3"
                                    style="height: 320px; overflow-y: auto;">
                                    @php $newBadgeInserted = false; @endphp
                                    @foreach($this->messages as $message)
                                        @if(!$newBadgeInserted && $firstUnreadMessageId && $message->id == $firstUnreadMessageId)
                                            @php $newBadgeInserted = true; @endphp
                                            <div class="text-center my-2"><span class="badge badge-pill badge-primary"
                                                    style="background: #0b93f6;">New Messages</span></div>
                                        @endif

                                        @php $isMine = $message->sender_id === auth()->id(); @endphp
                                        <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-4"
                                            wire:key="msg-{{ $message->id }}">
                                            @if(!$isMine)
                                                <div class="img_cont_msg"><img src="{{ $selectedFriend->getAvatarUrlAttribute() }}"
                                                        class="rounded-circle user_img_msg" style="width:34px; height:34px;"></div>
                                            @endif
                                            <div class="{{ $isMine ? 'msg_cotainer_send' : 'msg_cotainer' }}"
                                                style="max-width:70%;">
                                                @if($message->attachment_type === 'sticker')
                                                    <span style="font-size:2.5rem;">{{ $message->attachment_path }}</span>
                                                @elseif($message->attachment_type === 'image')
                                                    <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"><img
                                                            src="{{ asset('storage/' . $message->attachment_path) }}"
                                                            style="max-width:200px; border-radius:8px; display:block;"></a>
                                                    @if($message->body) <span>{{ $message->body }}</span> @endif
                                                @elseif($message->attachment_type === 'video')
                                                    <video controls style="max-width:200px; border-radius:8px;">
                                                        <source src="{{ asset('storage/' . $message->attachment_path) }}">
                                                    </video>
                                                    @if($message->body) <span>{{ $message->body }}</span> @endif
                                                @elseif($message->attachment_type === 'audio')
                                                    <audio controls style="width:100%;">
                                                        <source src="{{ asset('storage/' . $message->attachment_path) }}">
                                                    </audio>
                                                @elseif($message->attachment_type === 'document')
                                                    <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"
                                                        class="d-block"><i class="fa fa-file-pdf-o"></i>
                                                        {{ $message->attachment_name ?? 'Document' }}</a>
                                                @else
                                                    {{ $message->body }}
                                                @endif
                                                <span class="{{ $isMine ? 'msg_time_send' : 'msg_time' }}">
                                                    {{ $message->created_at->format('h:i A') }}
                                                    @if($isMine && $message->read) <i class="fa fa-check-circle text-primary"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            @if($isMine)
                                                <div class="img_cont_msg"><img src="{{ auth()->user()->getAvatarUrlAttribute() }}"
                                                        class="rounded-circle user_img_msg" style="width:34px; height:34px;"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Attachment preview --}}
                                @if($showAttachmentPreview)
                                    <div class="d-flex align-items-center px-3 py-2 border-top bg-white" style="gap:10px;">
                                        @if($attachmentType === 'image')
                                            <img src="{{ $attachmentPreview }}"
                                                style="width:44px; height:44px; object-fit:cover; border-radius:8px;">
                                        @else
                                            <div
                                                style="width:44px; height:44px; background:#e9ecef; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                                <i class="fa fa-file"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div style="font-weight:600; font-size:0.85rem;">{{ $attachmentName }}</div>
                                            <div style="font-size:0.75rem; color:#888;">{{ $attachmentType }}</div>
                                        </div>
                                        <button wire:click="clearAttachment" class="btn btn-sm btn-light"><i
                                                class="fa fa-times"></i></button>
                                    </div>
                                @endif

                                {{-- INPUT --}}
                                <div class="card-footer type_msg">
                                    <div class="input-group">
                                        <textarea class="form-control" placeholder="Type your message..." x-model="messageText"
                                            @keydown="handleKeydown($event)" @input="startTyping()" rows="1"
                                            style="resize:none;"></textarea>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" wire:click="sendMessage"
                                                @click="onSend()"><i class="fa fa-location-arrow"></i></button>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            @click="showEmoji = !showEmoji"><i class="fa fa-smile-o"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            @click="showAttachMenu = !showAttachMenu"><i class="fa fa-paperclip"></i></button>
                                        <div x-show="showEmoji" x-transition
                                            style="position:absolute; bottom:60px; left:10px; z-index:999; background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                                            <emoji-picker @emoji-click="addEmoji($event)"></emoji-picker>
                                        </div>
                                        <div x-show="showAttachMenu" x-transition
                                            style="position:absolute; bottom:60px; left:10px; z-index:999; background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.15); padding:10px; display:flex; gap:10px;">
                                            <label class="btn btn-sm btn-light"><i class="fa fa-image"></i> <input type="file"
                                                    wire:model="attachment" accept="image/*" class="d-none"></label>
                                            <label class="btn btn-sm btn-light"><i class="fa fa-video-camera"></i> <input
                                                    type="file" wire:model="attachment" accept="video/*" class="d-none"></label>
                                            <label class="btn btn-sm btn-light"><i class="fa fa-file-audio-o"></i> <input
                                                    type="file" wire:model="attachment" accept="audio/*" class="d-none"></label>
                                            <label class="btn btn-sm btn-light"><i class="fa fa-file-text-o"></i> <input
                                                    type="file" wire:model="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                                                    class="d-none"></label>
                                            <button class="btn btn-sm btn-light" @click="showStickers = !showStickers"><i
                                                    class="fa fa-smile-o"></i> Sticker</button>
                                        </div>
                                        <div x-show="showStickers" x-transition
                                            style="position:absolute; bottom:60px; left:10px; z-index:999; background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.15); padding:10px; display:flex; flex-wrap:wrap; gap:4px; max-width:200px;">
                                            @foreach(['😂', '😍', '🥰', '😎', '🤩', '😭', '🙏', '👏', '🔥', '💯', '❤️', '💔', '🎉', '🎊', '✨', '🌟', '😤', '😴', '🤔', '🥳', '🤯', '😱', '👀', '💪', '🙌', '😅', '🫶', '💀', '🤣', '🥹'] as $sticker)
                                                <button class="btn btn-sm btn-light" wire:click="sendSticker('{{ $sticker }}')"
                                                    @click="showStickers = false">{{ $sticker }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted p-4">
                                <p>User not found. <a href="#" wire:click="goBack">Go back</a></p>
                            </div>
                            @php $this->goBack(); @endphp
                        @endif
                    @endif
                </div>

                {{-- ═══ ALERTS TAB (restored) ═══ --}}
                <div class="tab-pane fade" id="alerts">
                    <div class="card mb-sm-3 mb-md-0 contacts_card">
                        <div class="card-header chat-list-header text-center">
                            <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="5" cy="12" r="2" />
                                        <circle fill="#000000" cx="12" cy="12" r="2" />
                                        <circle fill="#000000" cx="19" cy="12" r="2" />
                                    </g>
                                </svg></a>
                            <div>
                                <h6 class="mb-1">Notifications</h6>
                                <p class="mb-0">Show All</p>
                            </div>
                            <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="1" />
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg></a>
                        </div>
                        <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body1">
                            <ul class="contacts">
                                <li class="name-first-letter">SERVER STATUS</li>
                                <li class="active">
                                    <div class="d-flex bd-highlight">
                                        <div class="img_cont primary">KK</div>
                                        <div class="user_info">
                                            <span>David Nester Birthday</span>
                                            <p class="text-primary">Today</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="name-first-letter">SOCIAL</li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="img_cont success">RU</div>
                                        <div class="user_info">
                                            <span>Perfection Simplified</span>
                                            <p>Jame Smith commented on your status</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="name-first-letter">SERVER STATUS</li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="img_cont primary">AU</div>
                                        <div class="user_info">
                                            <span>AharlieKane</span>
                                            <p>Sami is online</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="img_cont info">MO</div>
                                        <div class="user_info">
                                            <span>Athan Jacoby</span>
                                            <p>Nargis left 30 mins ago</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>

                {{-- ═══ NOTES TAB (restored) ═══ --}}
                <div class="tab-pane fade" id="notes">
                    <div class="card mb-sm-3 mb-md-0 note_card">
                        <div class="card-header chat-list-header text-center">
                            <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                        <rect fill="#000000" opacity="1.0"
                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                            x="4" y="11" width="16" height="2" rx="1" />
                                    </g>
                                </svg></a>
                            <div>
                                <h6 class="mb-1">Notes</h6>
                                <p class="mb-0">Add New Notes</p>
                            </div>
                            <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="1" />
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,18 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg></a>
                        </div>
                        <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body2">
                            <ul class="contacts">
                                <li class="active">
                                    <div class="d-flex bd-highlight">
                                        <div class="user_info">
                                            <span>New order placed..</span>
                                            <p>10 Aug 2020</p>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i
                                                    class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="user_info">
                                            <span>Youtube, a video-sharing website..</span>
                                            <p>10 Aug 2020</p>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i
                                                    class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="user_info">
                                            <span>john just buy your product..</span>
                                            <p>10 Aug 2020</p>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i
                                                    class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex bd-highlight">
                                        <div class="user_info">
                                            <span>Athan Jacoby</span>
                                            <p>10 Aug 2020</p>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i
                                                    class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include call overlays --}}
    @auth
        @livewire('admin.messenger.call-overlays')
    @endauth

    {{-- ═══ ALPINE / ECHO / CALL MANAGER SCRIPTS ═══ --}}
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                if (!Alpine.store('chat')) {
                    // Defaults closed now that this widget is persisted across
                    // wire:navigate — it no longer needs to render pre-emptively.
                    Alpine.store('chat', { open: false });
                }
                window.chatState = chatState;
            });

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({ el }) => {
                    if (el && el.classList && el.classList.contains('chatbox') && window.Alpine?.store('chat')) {
                        el.style.display = Alpine.store('chat').open ? '' : 'none';
                    }
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
                    badge.classList.remove('dz-msg-badge-pop');
                    void badge.offsetWidth;
                    badge.classList.add('dz-msg-badge-pop');
                } else {
                    badge.style.display = 'none';
                }
            });

            // ═══════════════════════════════════════════════════════
            // NOTE: All Echo subscription logic now lives ONCE in the
            // shared `window.ChatBridge` object defined in the layout
            // (layouts/users.blade.php). This component no longer
            // declares its own `_currentChatChannel`, `getMessengerComponent()`,
            // or `subscribeToChat()` — those were the source of the bug
            // where messages only appeared live in whichever chat widget's
            // script happened to load/run last.
            // ═══════════════════════════════════════════════════════

            function chatState(initialFriendIds, friendNames) {
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

                        // NOTE: the old 'open-chatbox' window-event listener has been
                        // removed. The navbar's message icon now sets
                        // Alpine.store('chat').open = true directly, so this widget
                        // no longer needs to relay that event to itself — which
                        // also removes the dependency on this init() having already
                        // run by the time the icon is clicked.

                        this.$nextTick(() => window.forceBot());
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
                        this.showEmoji = false; this.showAttachMenu = false; this.showStickers = false;
                        window._autoScroll = true;
                        window.forceBot();
                    },
                    addEmoji(e) {
                        this.messageText += e.detail.unicode;
                        this.$nextTick(() => { if (this.$refs.input) this.$refs.input.focus(); });
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
                            this.showEmoji = false; this.showAttachMenu = false;
                            window._autoScroll = true;
                            window.forceBot();
                        }
                    },
                    startCall(type, friendId, friendName, friendAvatar) {
                        window.CallManager.startCall(type, friendId, friendName, friendAvatar);
                    },
                };
            }

            window._autoScroll = true;
            window.forceBot = function () {
                var el = document.getElementById('DZ_W_Contacts_Body3');
                if (el) el.scrollTop = el.scrollHeight;
            };

            window.addEventListener('scroll-to-bottom', function () {
                window._autoScroll = true;
                window.forceBot();
            });

            window.addEventListener('clear-input', function () {
                var el = document.querySelector('[x-model="messageText"]');
                if (el) el.value = '';
            });
        </script>
    @endpush
</div>