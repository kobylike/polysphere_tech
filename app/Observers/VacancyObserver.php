<?php

namespace App\Observers;

use App\Models\Vacancy;
use App\Services\ChatKnowledgeBase;

class VacancyObserver
{
    public function saved(Vacancy $vacancy): void
    {
        ChatKnowledgeBase::bust();
    }

    public function deleted(Vacancy $vacancy): void
    {
        ChatKnowledgeBase::bust();
    }

    public function restored(Vacancy $vacancy): void
    {
        ChatKnowledgeBase::bust();
    }
}
