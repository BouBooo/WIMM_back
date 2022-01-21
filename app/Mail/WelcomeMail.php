<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public const MAIL_CODE = 'wimm_welcome';

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): WelcomeMail
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
