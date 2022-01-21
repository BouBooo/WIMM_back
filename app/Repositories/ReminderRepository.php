<?php

namespace App\Repositories;

use App\Models\Reminder;
use Carbon\Carbon;

class ReminderRepository
{
    public function getSendableReminders()
    {
        return Reminder::whereDate('start_date', '=', Carbon::today())
            ->where('is_sent', false);
    }
}
