<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $description, array $properties = [], ?string $logName = 'default'): void
    {
        $activity = activity($logName)
            ->withProperties($properties);

        if (Auth::check()) {
            $activity->causedBy(Auth::user());
        }

        $activity->log($description);
    }
}