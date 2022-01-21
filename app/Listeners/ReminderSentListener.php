<?php

namespace App\Listeners;

use App\Mail\ReminderMail;
use App\Models\Reminder;

class ReminderSentListener
{
    public function handle(object $event): void
    {
        $mailType = $event->data['mailCode'];

        match ($mailType) {
            ReminderMail::MAIL_CODE => $this->updateReminderTask($event->data['reminder']),
            default => 'no-action'
        };
    }

    private function updateReminderTask(Reminder $reminder)
    {
        $reminder->update([
            'is_sent' => true,
        ]);
    }
}
