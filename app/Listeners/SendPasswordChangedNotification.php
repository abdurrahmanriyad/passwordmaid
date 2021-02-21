<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \App\Mail\PasswordChanged as PasswordChangedMail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param PasswordChanged $event
     */
    public function handle(PasswordChanged $event)
    {
        // no confirmation mail require for local
        if (App::environment('local')) {
            return false;
        }

        Mail::to($event->changedBy)
            ->send(new PasswordChangedMail());
    }
}
