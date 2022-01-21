<?php

namespace App\Observers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('password')) {
            $user->password = bcrypt($user->password);
            $user->saveQuietly();
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Mail::to($user->email)->queue(new WelcomeMail($user));
    }
}
