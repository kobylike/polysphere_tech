<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\ChatKnowledgeBase;

class ProjectObserver
{
    public function saved(Project $project): void
    {
        ChatKnowledgeBase::bust();
    }

    public function deleted(Project $project): void
    {
        ChatKnowledgeBase::bust();
    }

    public function restored(Project $project): void
    {
        ChatKnowledgeBase::bust();
    }
}
