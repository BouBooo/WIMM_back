<?php

namespace App\Observers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
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
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        Mail::to($user->email)->queue(new WelcomeMail($user));
    }
}
