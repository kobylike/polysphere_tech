<?php

namespace App\Policies;

use App\Models\ChatLead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatLeadPolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin bypasses every ability.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Standard CRUD                                              */
    /* ──────────────────────────────────────────────────────────── */

    public function viewAny(User $user): bool
    {
        return $user->can('View Chat Leads');
    }

    public function view(User $user, ChatLead $lead): bool
    {
        return $user->can('View Chat Leads');
    }

    /**
     * Leads are never created manually — they come from the chat widget.
     * Reserved for future "manual import" functionality.
     */
    public function create(User $user): bool
    {
        return $user->can('Edit Chat Leads');
    }

    public function update(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    public function delete(User $user, ChatLead $lead): bool
    {
        return $user->can('Delete Chat Leads');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('Delete Chat Leads');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Feature-specific abilities                                 */
    /* ──────────────────────────────────────────────────────────── */

    /**
     * Change pipeline status (new → contacted → qualified → converted/lost/spam).
     */
    public function changeStatus(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Add / edit internal notes.
     */
    public function manageNotes(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Add / remove tags.
     */
    public function manageTags(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Star / unstar a lead.
     */
    public function star(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Mark as spam / unmark spam.
     */
    public function markSpam(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Re-send the notification email for a lead.
     */
    public function resendNotification(User $user, ChatLead $lead): bool
    {
        return $user->can('Edit Chat Leads');
    }

    /**
     * Export leads to CSV.
     */
    public function export(User $user): bool
    {
        return $user->can('View Chat Leads');
    }
}
