<?php

namespace App\Providers;

use App\Events\GroupShared;
use App\Events\PasswordChanged;
use App\Listeners\SendGroupSharedNotification;
use App\Listeners\SendPasswordChangedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordChanged::class => [
            SendPasswordChangedNotification::class
        ],
        GroupShared::class => [
            SendGroupSharedNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}