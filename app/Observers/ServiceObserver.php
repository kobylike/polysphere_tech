<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\ChatKnowledgeBase;

class ServiceObserver
{
    public function saved(Service $service): void
    {
        ChatKnowledgeBase::bust();
    }

    public function deleted(Service $service): void
    {
        ChatKnowledgeBase::bust();
    }
}
