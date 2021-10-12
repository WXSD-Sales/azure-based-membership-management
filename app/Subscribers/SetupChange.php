<?php

namespace App\Subscribers;

use App\Events\SetupDone;
use App\Events\SetupUndone;
use App\Jobs\RetrieveWebexUsers;
use App\Notifications\SetupChanged;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Log;

class SetupChange
{

    public function handleSetupDone(SetupDone $event)
    {
        Log::info($event);
        $event->user->notify(new SetupChanged((string)$event));
    }

    public function handleSetupUndone(SetupUndone $event)
    {
        Log::info($event);
    }

    public function subscribe($events)
    {
        return [
            SetupDone::class => 'handleSetupDone',
            SetupUndone::class => 'handleSetupUndone',
        ];
    }
}
