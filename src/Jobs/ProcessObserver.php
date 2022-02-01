<?php

namespace SuStartX\JWTRedisMultiAuth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SuStartX\JWTRedisMultiAuth\Facades\RedisCache;

class ProcessObserver implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $model;

    private $process;

    public function __construct(Model $model, string $process)
    {
        $this->afterCommit = true;
        $this->model = $model;
        $this->process = $process;
    }

    public function handle()
    {
        $method = $this->process;

        $this->$method();
    }

    public function deleted()
    {
        return RedisCache::key($this->model->getRedisKey())->removeCache();
    }

    public function updated()
    {
        return RedisCache::key($this->model->getRedisKey())
            ->data($this->model->load(config('jwtredismultiauth.cache_relations')))
            ->refreshCache();
    }
}
