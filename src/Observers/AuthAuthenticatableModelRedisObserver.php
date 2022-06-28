<?php

namespace SuStartX\JWTRedisMultiAuth\Observers;

use Illuminate\Database\Eloquent\Model;
use SuStartX\JWTRedisMultiAuth\Jobs\ProcessObserver;

class AuthAuthenticatableModelRedisObserver
{
    public function retrieved(Model $model){
        // $this->handler($model, __FUNCTION__);
    }

    public function created(Model $model){
        // $this->handler($model, __FUNCTION__);
    }

    public function saved(Model $model){
        $this->handler($model, __FUNCTION__);
    }

    public function updated(Model $model){
        $this->handler($model, __FUNCTION__);
    }

    public function trashed(Model $model){
        // $this->handler($model, __FUNCTION__);
    }

    public function deleted(Model $model){
        $this->handler($model, __FUNCTION__);
    }

    public function forceDeleted(Model $model){
        $this->handler($model, __FUNCTION__);
    }

    public function restored(Model $model){
        // $this->handler($model, __FUNCTION__);
    }

    protected function handler(Model $model, string $action)
    {
        $handler = new ProcessObserver($model, $action);

        config('jwt_redis_multi_auth.observer_events_queue') ? dispatch($handler) : $handler->updated();
    }
}
