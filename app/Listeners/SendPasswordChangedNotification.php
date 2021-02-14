<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \App\Mail\PasswordChanged as PasswordChangedMail;
use Mail;

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
        Mail::to($event->changedBy)
            ->send(new PasswordChangedMail());
    }
}
