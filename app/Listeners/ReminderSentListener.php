<?php

namespace App\Listeners;

use App\Models\Reminder;

class ReminderSentListener
{
    public function handle(object $event): void
    {
        /** Triger only if mail is about reminders */
        if (isset($event->data['reminder'])) {
            $this->updateReminderTask($event->data['reminder']);
        }
    }

    private function updateReminderTask(Reminder $reminder)
    {
        $reminder->update([
            'is_sent' => true,
        ]);
    }
}
