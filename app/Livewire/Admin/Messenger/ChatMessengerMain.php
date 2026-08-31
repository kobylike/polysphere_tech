<?php

namespace App\Livewire\Admin\Messenger;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.users')]
class ChatMessengerMain extends Component
{
    use WithFileUploads;

    public        $friends;
    public ?int   $activeFriendId        = null;
    public string $messageText           = '';
    public int    $totalReceivedMessages = 0;
    public ?int   $firstUnreadMessageId  = null;
    public bool   $isLoading             = false;

    // Attachment upload
    public        $attachment            = null;
    public string $attachmentPreview     = '';
    public string $attachmentType        = '';
    public string $attachmentName        = '';
    public bool   $showAttachmentPreview = false;

    // Store friend IDs for JS subscription management
    public array $friendIds = [];

    // Presence: online user IDs from Reverb
    public array $onlineUserIds = [];

    // ─── LIFECYCLE ────────────────────────────────────────────────

    public function mount(?int $friendId = null): void
    {
        if (Auth::check()) {
            $this->updateUserOnlineStatus();
            $this->loadFriends();

            if ($friendId) {
                $this->setActiveFriend($friendId);
            }

            $this->dispatch('subscribe-to-presence');
        }
    }

    // ─── ONLINE / PRESENCE ────────────────────────────────────────

    public function updateUserOnlineStatus(): void
    {
        $userId = Auth::id();
        Cache::put('user-is-online-' . $userId, true, now()->addMinutes(5));
        Cache::put('user-last-seen-' . $userId, now(), now()->addMinutes(5));
    }

    // ─── FRIENDS LIST ─────────────────────────────────────────────

    public function loadFriends(): void
    {
        $userId = Auth::id();
        $usersWithUnread = 0;

        $users = User::where('id', '!=', $userId)->get()->map(
            function ($user) use ($userId, &$usersWithUnread) {
                $lastMessage = Message::where(function ($q) use ($userId, $user) {
                    $q->where('sender_id',   $userId)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($userId, $user) {
                    $q->where('sender_id',   $user->id)
                        ->where('receiver_id', $userId);
                })->latest()->first();

                $unreadCount = Message::where('sender_id',   $user->id)
                    ->where('receiver_id', $userId)
                    ->where('read', false)
                    ->count();

                if ($unreadCount > 0) {
                    $usersWithUnread++;
                }

                $user->last_message_time    = $lastMessage?->created_at;
                $user->message_count        = $unreadCount;
                $user->last_message_body    = $lastMessage?->body;
                $user->last_message_type    = $lastMessage?->attachment_type;
                $user->last_message_is_mine = $lastMessage?->sender_id === $userId;

                $user->online = $user->isOnline() || in_array($user->id, $this->onlineUserIds, true);

                return $user;
            }
        );

        $this->friends = $users
            ->sortByDesc(fn($u) => $u->last_message_time?->timestamp ?? 0)
            ->values();

        $this->totalReceivedMessages = $usersWithUnread;
        $this->friendIds             = $this->friends->pluck('id')->toArray();

        $this->dispatch('update-profile-subscriptions', friendIds: $this->friendIds);
        $this->dispatch('unread-count-updated', count: $this->totalReceivedMessages);
    }

    public function refreshFriendList(): void
    {
        $this->loadFriends();
    }

    public function updateFriendProfile(int $userId, string $newName, string $newAvatarUrl): void
    {
        $friend = $this->friends->firstWhere('id', $userId);
        if ($friend) {
            $friend->name = $newName;
            $friend->avatar = str_replace(asset('storage/'), '', $newAvatarUrl);
        }
        $this->friends = $this->friends;
    }

    #[On('updateOnlineUsers')]
    public function updateOnlineUsers(array $userIds): void
    {
        $this->onlineUserIds = $userIds;
        $this->loadFriends();
    }

    // ─── REALTIME EVENTS FROM ChatBridge (JS → Livewire, via Reverb) ──
    // Fired when a DIFFERENT user's browser sends us a message.
    // Reverb's ->toOthers() means these never fire in the sender's own
    // browser, which is why we also need `message-synced` below.

    #[On('message-received')]
    public function handleMessageReceived($senderId, $receiverId): void
    {
        $senderId   = (int) $senderId;
        $receiverId = (int) $receiverId;

        if ($receiverId !== Auth::id()) {
            return;
        }

        if ($senderId === $this->activeFriendId) {
            $this->receiveMessage();
        } else {
            $this->loadFriends();
        }
    }

    // ─── LOCAL SYNC BETWEEN THIS USER'S OWN OPEN WIDGETS ──────────
    // Fired (server-side, via Livewire's own event bus — NOT Reverb)
    // right after THIS user sends a message from ANY widget. Because
    // broadcast(...)->toOthers() intentionally never echoes back to
    // the sender's own browser, without this a user who has both the
    // navbar chat AND the full-page messenger open at once would only
    // see their own sent message in whichever widget they typed it in.
    // Livewire delivers dispatched events to every mounted component
    // on the page, so the sibling widget picks this up instantly.

    #[On('message-synced')]
    public function handleMessageSynced($senderId, $receiverId): void
    {
        if ((int) $senderId !== Auth::id()) {
            return; // safety guard — this event is only for our own sends
        }

        $receiverId = (int) $receiverId;

        if ($receiverId === $this->activeFriendId) {
            $this->loadFriends();
            $this->dispatch('scroll-to-bottom');
        } else {
            $this->loadFriends();
        }
    }

    #[On('friend-list-refresh-needed')]
    public function handleFriendListRefreshNeeded(): void
    {
        $this->loadFriends();
    }

    #[On('friend-profile-updated')]
    public function handleFriendProfileUpdated($userId, $name, $avatarUrl): void
    {
        $this->updateFriendProfile((int) $userId, $name, $avatarUrl);
    }

    // ─── OPEN / CLOSE CONVERSATION ──────────────────────────────

    public function setActiveFriend(int $friendId): void
    {
        if ($this->isLoading) return;
        $this->isLoading = true;

        try {
            $this->activeFriendId = $friendId;
            $this->clearAttachment();

            $firstUnread = Message::where('sender_id',   $friendId)
                ->where('receiver_id', Auth::id())
                ->where('read', false)
                ->oldest()
                ->first();

            $this->firstUnreadMessageId = $firstUnread?->id;

            Message::where('sender_id',   $friendId)
                ->where('receiver_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true]);

            $this->loadFriends();
            $this->updateUserOnlineStatus();

            $this->dispatch('friend-selected', friendId: $friendId);
            $this->dispatch('scroll-to-bottom');
        } finally {
            $this->isLoading = false;
        }
    }

    public function goBack(): void
    {
        $this->activeFriendId       = null;
        $this->firstUnreadMessageId = null;
        $this->clearAttachment();
        $this->loadFriends();

        $this->dispatch('chat-closed');
    }

    // ─── ATTACHMENT HANDLING ─────────────────────────────────────

    public function updatedAttachment(): void
    {
        if (! $this->attachment) return;

        $mime = $this->attachment->getMimeType();
        $this->attachmentName = $this->attachment->getClientOriginalName();

        if (str_starts_with($mime, 'image/')) {
            $this->attachmentType    = 'image';
            $this->attachmentPreview = $this->attachment->temporaryUrl();
        } elseif (str_starts_with($mime, 'video/')) {
            $this->attachmentType    = 'video';
            $this->attachmentPreview = '';
        } elseif (str_starts_with($mime, 'audio/')) {
            $this->attachmentType    = 'audio';
            $this->attachmentPreview = '';
        } else {
            $this->attachmentType    = 'document';
            $this->attachmentPreview = '';
        }

        $this->showAttachmentPreview = true;
        $this->dispatch('attachment-ready');
    }

    public function clearAttachment(): void
    {
        $this->attachment            = null;
        $this->attachmentPreview     = '';
        $this->attachmentType        = '';
        $this->attachmentName        = '';
        $this->showAttachmentPreview = false;
    }

    // ─── SEND STICKER ─────────────────────────────────────────────

    public function sendSticker(string $emoji): void
    {
        if (! $this->activeFriendId) return;

        $message = Message::create([
            'sender_id'       => Auth::id(),
            'receiver_id'     => $this->activeFriendId,
            'body'            => '',
            'attachment_type' => 'sticker',
            'attachment_path' => $emoji,
            'read'            => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $this->loadFriends();
        $this->updateUserOnlineStatus();

        $this->dispatch('scroll-to-bottom');
        $this->dispatch('refresh-notifications');

        // Sync this user's OTHER open chat widgets (e.g. navbar popup)
        // since ->toOthers() above never echoes back to us.
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $this->activeFriendId);
    }

    // ─── SEND MESSAGE ─────────────────────────────────────────────

    public function sendMessage(): void
    {
        $this->messageText = trim($this->messageText);

        if (! $this->messageText && ! $this->attachment) return;
        if (! $this->activeFriendId) return;

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentSize = null;
        $attachmentType = null;

        if ($this->attachment) {
            $attachmentType = $this->attachmentType;
            $attachmentName = $this->attachmentName;
            $attachmentSize = $this->attachment->getSize();

            $folder = match ($attachmentType) {
                'image' => 'messages/images',
                'video' => 'messages/videos',
                'audio' => 'messages/audio',
                default => 'messages/documents',
            };

            $attachmentPath = $this->attachment->store($folder, 'public');
        }

        $message = Message::create([
            'sender_id'       => Auth::id(),
            'receiver_id'     => $this->activeFriendId,
            'body'            => $this->messageText,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
            'read'            => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $activeFriendIdAtSendTime = $this->activeFriendId;

        $this->reset('messageText');
        $this->clearAttachment();

        Cache::forget('user-typing-' . Auth::id() . '-to-' . $activeFriendIdAtSendTime);

        $this->loadFriends();
        $this->updateUserOnlineStatus();

        $this->dispatch('scroll-to-bottom');
        $this->dispatch('clear-input');
        $this->dispatch('refresh-notifications');

        // Sync this user's OTHER open chat widgets (e.g. navbar popup)
        // since ->toOthers() above never echoes back to us.
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $activeFriendIdAtSendTime);
    }

    // ─── RECEIVE MESSAGE ──────────────────────────────────────────

    public function receiveMessage(): void
    {
        if (! $this->activeFriendId) return;

        Message::where('sender_id',   $this->activeFriendId)
            ->where('receiver_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        $this->loadFriends();
        $this->updateUserOnlineStatus();

        $this->dispatch('scroll-to-bottom');
    }

    // ─── TYPING INDICATOR ─────────────────────────────────────────

    public function startTyping(): void
    {
        if (! $this->activeFriendId) return;

        Cache::put(
            'user-typing-' . Auth::id() . '-to-' . $this->activeFriendId,
            true,
            now()->addSeconds(5)
        );
    }

    public function isFriendTyping(): bool
    {
        if (! $this->activeFriendId) return false;

        return Cache::has('user-typing-' . $this->activeFriendId . '-to-' . Auth::id());
    }

    // ─── COMPUTED PROPERTIES ──────────────────────────────────────

    public function getMessagesProperty()
    {
        if (! $this->activeFriendId) return collect();

        return Message::where(function ($q) {
            $q->where('sender_id',   Auth::id())
                ->where('receiver_id', $this->activeFriendId);
        })->orWhere(function ($q) {
            $q->where('sender_id',   $this->activeFriendId)
                ->where('receiver_id', Auth::id());
        })->oldest()->get();
    }

    public function getMediaMessagesProperty()
    {
        return $this->messages
            ->whereIn('attachment_type', ['image', 'video'])
            ->sortByDesc('created_at')
            ->values();
    }

    public function getFileMessagesProperty()
    {
        return $this->messages
            ->where('attachment_type', 'document')
            ->sortByDesc('created_at')
            ->values();
    }
    public function render()
    {
        return view('livewire.admin.messenger.chat-messenger-main');
    }
}
