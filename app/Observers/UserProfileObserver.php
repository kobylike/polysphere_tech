<?php

namespace App\Observers;

use App\Models\UserProfile;
use App\Services\ChatKnowledgeBase;

class UserProfileObserver
{
    public function saved(UserProfile $profile): void
    {
        ChatKnowledgeBase::bust();
    }

    public function deleted(UserProfile $profile): void
    {
        ChatKnowledgeBase::bust();
    }
}
