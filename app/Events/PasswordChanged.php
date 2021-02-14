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

class PasswordChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $changedBy;

    public function __construct(User $changedBy)
    {
        $this->changedBy = $changedBy;
    }
}
