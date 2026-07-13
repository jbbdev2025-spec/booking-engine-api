<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Domain\Booking\Events\BookingCreated;
use App\Listeners\Booking\BookingCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [

        BookingCreated::class => [

            BookingCreatedListener::class,

        ],

    ];
}