<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Domain\Booking\Events\BookingCreated;
use App\Listeners\Booking\BookingProjectionListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        BookingCreated::class => [

            BookingProjectionListener::class,

        ],

    ];
}