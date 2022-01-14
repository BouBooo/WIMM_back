<?php

namespace App\Listeners;

class ReminderSentListener
{
    public function handle(object $event): void
    {
        $reminder = $event->data['reminder'];
        $reminder->update([
            'is_sent' => true
        ]);
    }
}
