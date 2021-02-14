<?php

namespace App\Listeners;

use App\Events\GroupShared;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \App\Mail\GroupShared as GroupSharedMail;
use Mail;

class SendGroupSharedNotification implements ShouldQueue
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
     * @param GroupShared $groupShared
     */
    public function handle(GroupShared $groupShared)
    {
        Mail::to($groupShared->sharedTo)
            ->send(new GroupSharedMail($groupShared->sharedBy));
    }
}
