<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\DuplicateFundWarning;
use App\Listeners\DuplicateFundWarningListener;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [

        # Warning duplicated Funds
        DuplicateFundWarning::class => [
            DuplicateFundWarningListener::class,
        ],

    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
