<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public const MAIL_CODE = 'wimm_reset_password_request_send';

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function build(): static
    {
        return $this->from(config('mail.mailers.smtp.from'))
            ->subject('WIMM: Reset password request !')
            ->view('emails.reset_password_request')
            ->with([
                'token' => $this->token,
                'mailCode' => self::MAIL_CODE
            ]);
    }
}
