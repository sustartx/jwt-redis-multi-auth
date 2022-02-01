<?php

namespace SuStartX\JWTRedisMultiAuth\Observers;

use Illuminate\Database\Eloquent\Model;
use SuStartX\JWTRedisMultiAuth\Jobs\ProcessObserver;

class AuthAuthenticatableModelRedisObserver
{
    protected function handler(Model $model, string $action)
    {
        $handler = new ProcessObserver($model, $action);

        config('jwtredismultiauth.observer_events_queue') ? dispatch($handler) : $handler->updated();
    }

    public function creating(Model $model)
    {

    }

    public function created(Model $model)
    {

    }

    public function updated(Model $model)
    {
        $this->handler($model, __FUNCTION__);
    }

    public function deleted(Model $model)
    {
        $this->handler($model, __FUNCTION__);
    }

    public function restored(Model $model)
    {

    }

    public function forceDeleted(Model $model)
    {

    }
}
