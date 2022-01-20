<?php

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reminder $reminder;

    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->from(config('mail.mailers.smtp.from'))
            ->subject('WIMM: You have a reminder !')
            ->view('emails.reminder')
            ->with(['reminder', $this->reminder]);
    }
}
