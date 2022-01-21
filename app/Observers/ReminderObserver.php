<?php

namespace App\Observers;

use App\Models\Reminder;
use Carbon\Carbon;

class ReminderObserver
{
    /**
     * Handle the Reminder "updating" event.
     */
    public function updating(Reminder $reminder): void
    {
        $reminder->start_date = Carbon::create($reminder->start_date);
        $reminder->end_date = Carbon::create($reminder->end_date);
        $reminder->saveQuietly();
    }
}
