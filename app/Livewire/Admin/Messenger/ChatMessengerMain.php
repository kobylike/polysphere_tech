<?php

namespace App\Livewire\Admin\Messenger;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\MessageDraft;
use App\Models\MessageReaction;
use App\Models\MessageUserState;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.users')]
class ChatMessengerMain extends Component
{
    use WithFileUploads;

    // Core
    public $friends;
    public ?int $activeFriendId = null;
    public string $messageText  = '';
    public int $totalReceivedMessages = 0;
    public ?int $firstUnreadMessageId = null;
    public bool $isLoading = false;
    public array $friendIds = [];
    public array $onlineUserIds = [];

    // Attachments
    public $attachment = null;
    public string $attachmentPreview = '';
    public string $attachmentType    = '';
    public string $attachmentName    = '';
    public bool $showAttachmentPreview = false;

    // Reply/Edit/Forward state
    public ?int $replyToId   = null;
    public ?int $editingId   = null;
    public ?int $forwardingId = null;

    // Voice recorder state
    public bool $isRecording = false;
    public int $recordingSeconds = 0;

    // Search / Pagination
    public string $searchQuery   = '';
    public bool $searchOpen      = false;
    public int $messagesLimit    = 40;
    public bool $hasMoreMessages = false;

    // UI state
    public bool $darkMode     = false;
    public string $wallpaper  = 'default';
    public bool $showPinnedBar = false;
    public bool $showStarredOnly = false;

    // Emoji quick reactions
    public array $quickReactions = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

    // ─── Lifecycle ─────────────────────────────────────────────
    public function mount(?int $friendId = null): void
    {
        if (! Auth::check()) return;

        $this->darkMode  = (bool) Cache::get('chat-dark-' . Auth::id(), false);
        $this->wallpaper = Cache::get('chat-wallpaper-' . Auth::id(), 'default');

        $this->updateUserOnlineStatus();
        $this->loadFriends();

        if ($friendId) $this->setActiveFriend($friendId);

        $this->dispatch('subscribe-to-presence');
    }

    public function updateUserOnlineStatus(): void
    {
        $id = Auth::id();
        Cache::put("user-is-online-{$id}", true, now()->addMinutes(5));
        Cache::put("user-last-seen-{$id}", now(), now()->addMinutes(5));
    }

    // ─── Friends ───────────────────────────────────────────────
    public function loadFriends(): void
    {
        $userId = Auth::id();
        $unreadTotal = 0;

        $users = User::where('id', '!=', $userId)->get()->map(function ($user) use ($userId, &$unreadTotal) {
            $lastMessage = Message::withTrashed()
                ->where(fn($q) => $q->where('sender_id', $userId)->where('receiver_id', $user->id))
                ->orWhere(fn($q) => $q->where('sender_id', $user->id)->where('receiver_id', $userId))
                ->latest()->first();

            $unread = Message::where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->where('read', false)
                ->count();

            if ($unread > 0) $unreadTotal++;

            $user->last_message_time    = $lastMessage?->created_at;
            $user->message_count        = $unread;
            $user->last_message_body    = $lastMessage?->body;
            $user->last_message_type    = $lastMessage?->attachment_type;
            $user->last_message_is_mine = $lastMessage?->sender_id === $userId;
            $user->online               = $user->isOnline() || in_array($user->id, $this->onlineUserIds, true);

            return $user;
        });

        $this->friends = $users->sortByDesc(fn($u) => $u->last_message_time?->timestamp ?? 0)->values();
        $this->totalReceivedMessages = $unreadTotal;
        $this->friendIds             = $this->friends->pluck('id')->toArray();

        $this->dispatch('update-profile-subscriptions', friendIds: $this->friendIds);
        $this->dispatch('unread-count-updated', count: $this->totalReceivedMessages);
    }

    public function refreshFriendList(): void
    {
        $this->loadFriends();
    }

    #[On('updateOnlineUsers')]
    public function updateOnlineUsers(array $userIds): void
    {
        $this->onlineUserIds = $userIds;
        $this->loadFriends();
    }

    public function updateFriendProfile(int $userId, string $newName, string $newAvatarUrl): void
    {
        if ($friend = $this->friends->firstWhere('id', $userId)) {
            $friend->name   = $newName;
            $friend->avatar = str_replace(asset('storage/'), '', $newAvatarUrl);
        }
        $this->friends = $this->friends;
    }

    // ─── Realtime ──────────────────────────────────────────────
    #[On('message-received')]
    public function handleMessageReceived($senderId, $receiverId): void
    {
        $senderId   = (int) $senderId;
        $receiverId = (int) $receiverId;
        if ($receiverId !== Auth::id()) return;

        if ($senderId === $this->activeFriendId) {
            $this->receiveMessage();
        } else {
            $this->loadFriends();
        }
    }

    #[On('message-synced')]
    public function handleMessageSynced($senderId, $receiverId): void
    {
        if ((int) $senderId !== Auth::id()) return;
        $this->loadFriends();
        if ((int) $receiverId === $this->activeFriendId) {
            $this->dispatch('scroll-to-bottom');
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

    #[On('message-updated')]
    public function handleMessageUpdated(): void
    {
        $this->reloadMessages();
    }

    // ─── Select friend ─────────────────────────────────────────
    public function setActiveFriend(int $friendId): void
    {
        if ($this->isLoading) return;
        $this->isLoading = true;

        try {
            $this->activeFriendId = $friendId;
            $this->clearAttachment();
            $this->cancelReply();
            $this->cancelEdit();
            $this->searchQuery = '';
            $this->searchOpen = false;
            $this->messagesLimit = 40;
            $this->showStarredOnly = false;

            $firstUnread = Message::where('sender_id', $friendId)
                ->where('receiver_id', Auth::id())
                ->where('read', false)
                ->oldest()->first();

            $this->firstUnreadMessageId = $firstUnread?->id;

            Message::where('sender_id', $friendId)
                ->where('receiver_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true, 'delivered_at' => now()]);

            // Load draft
            $this->messageText = MessageDraft::where('user_id', Auth::id())
                ->where('friend_id', $friendId)
                ->value('body') ?? '';

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
        $this->saveDraft();
        $this->activeFriendId = null;
        $this->firstUnreadMessageId = null;
        $this->messageText = '';
        $this->searchQuery = '';
        $this->searchOpen = false;
        $this->cancelReply();
        $this->cancelEdit();
        $this->clearAttachment();
        $this->loadFriends();
        $this->dispatch('chat-closed');
    }

    // ─── Draft ─────────────────────────────────────────────────
    public function updatedMessageText(): void
    {
        // Debounced autosave via Alpine dispatch in blade
    }

    public function saveDraft(): void
    {
        if (! $this->activeFriendId) return;

        MessageDraft::updateOrCreate(
            ['user_id' => Auth::id(), 'friend_id' => $this->activeFriendId],
            ['body'    => trim($this->messageText) ?: null]
        );
    }

    // ─── Reply / Edit ──────────────────────────────────────────
    public function setReply(int $messageId): void
    {
        $this->editingId = null;
        $this->replyToId = $messageId;
        $this->dispatch('focus-input');
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
    }

    public function setEdit(int $messageId): void
    {
        $msg = Message::find($messageId);
        if (! $msg || $msg->sender_id !== Auth::id()) return;

        $this->replyToId   = null;
        $this->editingId   = $messageId;
        $this->messageText = $msg->body;
        $this->dispatch('focus-input');
    }

    public function cancelEdit(): void
    {
        $this->editingId   = null;
        $this->messageText = '';
    }

    public function saveEdit(): void
    {
        if (! $this->editingId) return;

        $msg = Message::find($this->editingId);
        if (! $msg || $msg->sender_id !== Auth::id()) return;

        $body = trim($this->messageText);
        if ($body === '') return;

        $msg->update(['body' => $body, 'edited_at' => now()]);

        broadcast(new MessageSent($msg))->toOthers();

        $this->editingId   = null;
        $this->messageText = '';
        $this->reloadMessages();
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $this->activeFriendId);
    }

    // ─── Delete ────────────────────────────────────────────────
    public function deleteForMe(int $messageId): void
    {
        MessageUserState::updateOrCreate(
            ['message_id' => $messageId, 'user_id' => Auth::id()],
            ['is_deleted' => true]
        );
        $this->reloadMessages();
    }

    public function deleteForEveryone(int $messageId): void
    {
        $msg = Message::find($messageId);
        if (! $msg || $msg->sender_id !== Auth::id()) return;

        $msg->update([
            'body'                  => '',
            'deleted_for_everyone'  => true,
            'attachment_path'       => null,
            'attachment_type'       => null,
            'attachment_name'       => null,
        ]);

        broadcast(new MessageSent($msg))->toOthers();

        $this->reloadMessages();
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $this->activeFriendId);
    }

    // ─── Star / Pin ────────────────────────────────────────────
    public function toggleStar(int $messageId): void
    {
        $state = MessageUserState::firstOrCreate(
            ['message_id' => $messageId, 'user_id' => Auth::id()],
            ['is_starred' => false]
        );
        $state->update(['is_starred' => ! $state->is_starred]);
        $this->reloadMessages();
    }

    public function togglePin(int $messageId): void
    {
        $msg = Message::find($messageId);
        if (! $msg) return;
        $msg->update(['is_pinned' => ! $msg->is_pinned]);
        $this->reloadMessages();
    }

    public function toggleStarredView(): void
    {
        $this->showStarredOnly = ! $this->showStarredOnly;
    }

    // ─── Forward ───────────────────────────────────────────────
    public function startForward(int $messageId): void
    {
        $this->forwardingId = $messageId;
        $this->dispatch('open-forward-modal', messageId: $messageId);
    }

    public function forwardTo(int $targetFriendId): void
    {
        if (! $this->forwardingId) return;

        $src = Message::find($this->forwardingId);
        if (! $src) return;

        $new = Message::create([
            'sender_id'       => Auth::id(),
            'receiver_id'     => $targetFriendId,
            'body'            => $src->body,
            'attachment_path' => $src->attachment_path,
            'attachment_type' => $src->attachment_type,
            'attachment_name' => $src->attachment_name,
            'attachment_size' => $src->attachment_size,
            'is_forwarded'    => true,
            'read'            => false,
        ]);

        broadcast(new MessageSent($new))->toOthers();

        $this->forwardingId = null;
        $this->loadFriends();
        $this->dispatch('close-forward-modal');
        $this->dispatch('toast', message: 'Forwarded', type: 'success');
    }

    // ─── Copy ──────────────────────────────────────────────────
    public function copyMessage(int $messageId): void
    {
        $body = Message::find($messageId)?->body;
        if ($body) $this->dispatch('copy-to-clipboard', text: $body);
    }

    // ─── Reactions ─────────────────────────────────────────────
    public function toggleReaction(int $messageId, string $emoji): void
    {
        $existing = MessageReaction::where('message_id', $messageId)
            ->where('user_id', Auth::id())
            ->where('emoji', $emoji)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            MessageReaction::create([
                'message_id' => $messageId,
                'user_id'    => Auth::id(),
                'emoji'      => $emoji,
            ]);
        }

        broadcast(new \App\Events\MessageReactionToggled(
            $messageId,
            Auth::id(),
            $this->activeFriendId ?? 0,
            $emoji
        ))->toOthers();

        $this->reloadMessages();
    }

    #[On('reaction-toggled')]
    public function onRemoteReaction(): void
    {
        $this->reloadMessages();
    }

    // ─── Voice / Attachments ───────────────────────────────────
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
        $this->attachment = null;
        $this->attachmentPreview = '';
        $this->attachmentType = '';
        $this->attachmentName = '';
        $this->showAttachmentPreview = false;
    }

    public function setRecording(bool $state): void
    {
        $this->isRecording = $state;
    }

    public function sendVoiceNote($dataUrl, int $duration = 0): void
    {
        if (! $this->activeFriendId || ! $dataUrl) return;

        // data:audio/webm;base64,....
        if (! preg_match('/^data:audio\/(\w+);base64,(.+)$/', $dataUrl, $m)) return;

        $ext      = $m[1] === 'webm' ? 'webm' : 'ogg';
        $binary   = base64_decode($m[2]);
        $filename = 'voice_' . uniqid() . '.' . $ext;
        $path     = 'messages/audio/' . $filename;

        Storage::disk('public')->put($path, $binary);

        $message = Message::create([
            'sender_id'       => Auth::id(),
            'receiver_id'     => $this->activeFriendId,
            'body'            => '',
            'attachment_path' => $path,
            'attachment_type' => 'audio',
            'attachment_name' => $filename,
            'attachment_size' => strlen($binary),
            'metadata'        => ['voice' => true, 'duration' => $duration],
            'read'            => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $this->reloadMessages();
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $this->activeFriendId);
    }

    // ─── Send Sticker / Message ────────────────────────────────
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
        $this->afterSend();
    }

    public function sendMessage(): void
    {
        $this->messageText = trim($this->messageText);

        if ($this->editingId) {
            $this->saveEdit();
            return;
        }
        if (! $this->messageText && ! $this->attachment) return;
        if (! $this->activeFriendId) return;

        $attachmentPath = $attachmentName = $attachmentType = null;
        $attachmentSize = null;

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
            'reply_to_id'     => $this->replyToId,
            'body'            => $this->messageText,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
            'read'            => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        // Clear draft
        MessageDraft::where('user_id', Auth::id())
            ->where('friend_id', $this->activeFriendId)
            ->delete();

        $this->reset('messageText');
        $this->cancelReply();
        $this->clearAttachment();

        Cache::forget('user-typing-' . Auth::id() . '-to-' . $this->activeFriendId);

        $this->afterSend();
    }

    protected function afterSend(): void
    {
        $friendId = $this->activeFriendId;
        $this->reloadMessages();
        $this->updateUserOnlineStatus();
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('clear-input');
        $this->dispatch('refresh-notifications');
        $this->dispatch('message-synced', senderId: Auth::id(), receiverId: $friendId);
    }

    public function receiveMessage(): void
    {
        if (! $this->activeFriendId) return;

        Message::where('sender_id', $this->activeFriendId)
            ->where('receiver_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true, 'delivered_at' => now()]);

        $this->reloadMessages();
        $this->updateUserOnlineStatus();
        $this->dispatch('scroll-to-bottom');
    }

    // ─── Typing ────────────────────────────────────────────────
    public function startTyping(): void
    {
        if (! $this->activeFriendId) return;
        Cache::put('user-typing-' . Auth::id() . '-to-' . $this->activeFriendId, true, now()->addSeconds(5));
    }

    public function isFriendTyping(): bool
    {
        if (! $this->activeFriendId) return false;
        return Cache::has('user-typing-' . $this->activeFriendId . '-to-' . Auth::id());
    }

    // ─── Search / Pagination ───────────────────────────────────
    /**
     * Fires automatically whenever $searchQuery changes.
     * Trims input, resets pagination, and scrolls to top so the
     * user always sees the first match.
     */
    public function updatedSearchQuery(): void
    {
        $this->searchQuery   = trim($this->searchQuery);
        $this->messagesLimit = 40;
        $this->dispatch('scroll-to-bottom');
    }

    public function toggleSearch(): void
    {
        $this->searchOpen = ! $this->searchOpen;
        if (! $this->searchOpen) $this->searchQuery = '';
    }

    public function loadMore(): void
    {
        $this->messagesLimit += 40;
        $this->reloadMessages();
        $this->dispatch('preserve-scroll');
    }

    public function reloadMessages(): void
    {
        // Invalidate the cached collection so the computed property
        // re-queries on the next render.
        $this->hasMoreMessages = false;
        $this->dispatch('scroll-to-bottom');
    }

    // ─── Theme ─────────────────────────────────────────────────
    public function toggleDarkMode(): void
    {
        $this->darkMode = ! $this->darkMode;
        Cache::put('chat-dark-' . Auth::id(), $this->darkMode, now()->addYear());
        $this->dispatch('theme-changed', dark: $this->darkMode);
    }

    public function setWallpaper(string $wallpaper): void
    {
        $this->wallpaper = $wallpaper;
        Cache::put('chat-wallpaper-' . Auth::id(), $wallpaper, now()->addYear());
    }

    // ─── Computed ──────────────────────────────────────────────
    public function getMessagesProperty()
    {
        if (! $this->activeFriendId) return collect();

        $userId   = Auth::id();
        $friendId = $this->activeFriendId;

        $q = Message::with(['reactions', 'userStates', 'replyTo'])
            ->where(function ($q) use ($userId, $friendId) {
                $q->where('sender_id', $userId)->where('receiver_id', $friendId);
            })->orWhere(function ($q) use ($userId, $friendId) {
                $q->where('sender_id', $friendId)->where('receiver_id', $userId);
            });

        // ── Search filter ──
        if ($this->searchQuery !== '') {
            // Escape LIKE special chars so a literal "%" or "_" doesn't
            // turn into a wildcard.
            $escaped = addcslashes($this->searchQuery, '%_\\');
            $q->where('body', 'like', '%' . $escaped . '%');
        }

        if ($this->showStarredOnly) {
            $q->whereHas('userStates', function ($sq) use ($userId) {
                $sq->where('user_id', $userId)->where('is_starred', true);
            });
        }

        $messages = $q->orderByDesc('id')->limit($this->messagesLimit + 1)->get();

        $this->hasMoreMessages = $messages->count() > $this->messagesLimit;
        $messages = $messages->take($this->messagesLimit)->reverse()->values();

        return $messages->reject(fn($m) => $m->isDeletedBy($userId));
    }

    public function getPinnedMessagesProperty()
    {
        if (! $this->activeFriendId) return collect();
        $userId = Auth::id();
        return Message::where('is_pinned', true)
            ->where(function ($q) use ($userId) {
                $q->where(fn($x) => $x->where('sender_id', $userId)->where('receiver_id', $this->activeFriendId))
                    ->orWhere(fn($x) => $x->where('sender_id', $this->activeFriendId)->where('receiver_id', $userId));
            })->latest()->limit(3)->get();
    }

    public function getMediaMessagesProperty()
    {
        return $this->messages->whereIn('attachment_type', ['image', 'video'])
            ->sortByDesc('created_at')->values();
    }

    public function getFileMessagesProperty()
    {
        return $this->messages->where('attachment_type', 'document')
            ->sortByDesc('created_at')->values();
    }

    public function getVoiceMessagesProperty()
    {
        return $this->messages->where('attachment_type', 'audio')
            ->sortByDesc('created_at')->values();
    }

    public function render()
    {
        return view('livewire.admin.messenger.chat-messenger-main');
    }
}
