<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Message;

// Private channel for a specific user – used for profile updates
Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
    if ((int) $user->id === (int) $userId) {
        return $user;
    }

    $hasConversation = Message::where(function ($q) use ($user, $userId) {
        $q->where('sender_id', $user->id)->where('receiver_id', $userId);
    })->orWhere(function ($q) use ($user, $userId) {
        $q->where('sender_id', $userId)->where('receiver_id', $user->id);
    })->exists();

    return $hasConversation ? $user : null;
});

// Chat channel
Broadcast::channel('chat.{id1}.{id2}', function ($user, $id1, $id2) {
    return $user->id === (int)$id1 || $user->id === (int)$id2;
});

// Notifications channel
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// WebRTC call signalling channel
Broadcast::channel('call.{id1}.{id2}', function ($user, $id1, $id2) {
    return $user->id === (int)$id1 || $user->id === (int)$id2;
});

// ── Presence channel for online users ─────────────────────────────
Broadcast::channel('presence-online', function ($user) {
    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->avatar_url, // make sure this attribute exists
    ];
});
