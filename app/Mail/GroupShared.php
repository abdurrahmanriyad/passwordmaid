<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class GroupShared extends Mailable
{
    use Queueable, SerializesModels;

    public $sharedBy;
    /**
     * GroupShared constructor.
     * @param User $sharedBy
     */
    public function __construct(User $sharedBy)
    {
        $this->sharedBy = $sharedBy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('New group shared')
            ->markdown('emails.groups.shared');
    }
}
