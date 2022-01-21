<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    const MAIL_CODE = 'wimm_welcome';

    public User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.mailers.smtp.from'))
            ->subject('WIMM: Welcome !')
            ->view('emails.welcome')
            ->with([
                'user' => $this->user,
                'mailCode' => self::MAIL_CODE
            ]);
    }
}
