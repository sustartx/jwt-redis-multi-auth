<?php

namespace SuStartX\JWTRedisMultiAuth\Facades;

use Illuminate\Support\Facades\Facade;
use SuStartX\JWTRedisMultiAuth\Contracts\RedisCacheContract;

class RedisCache extends Facade
{
    public static function getFacadeAccessor()
    {
        return RedisCacheContract::class;
    }
}
