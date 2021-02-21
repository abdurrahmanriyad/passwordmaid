<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\App;

class SendEmailVerificationNotification
{
    public function handle(Registered $event)
    {
        // verifies without sending email for local development
        if (App::environment('local')) {
            return $event->user->markEmailAsVerified();
        }

        if ($event->user instanceof MustVerifyEmail && !$event->user->hasVerifiedEmail()) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
