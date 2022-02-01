<?php

namespace SuStartX\JWTRedisMultiAuth\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SuStartX\JWTRedisMultiAuth\Events\LoggedInEvent;
use SuStartX\JWTRedisMultiAuth\Listeners\LoggedInListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        LoggedInEvent::class => [
            LoggedInListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}
