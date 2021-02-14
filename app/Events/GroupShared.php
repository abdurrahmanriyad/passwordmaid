<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\User;

class GroupShared
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sharedBy;
    public $sharedTo;


    public function __construct(User $sharedBy, User $sharedTo)
    {
        $this->sharedBy = $sharedBy;
        $this->sharedTo = $sharedTo;
    }
}
