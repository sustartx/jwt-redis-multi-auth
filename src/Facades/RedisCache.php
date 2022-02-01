<?php

namespace SuStartX\JWTRedisMultiAuth\Facades;

use Illuminate\Support\Facades\Facade;
use SuStartX\JWTRedisMultiAuth\Contracts\RedisCacheContract;

/**
 * Class RedisCache.
 */
class RedisCache extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return RedisCacheContract::class;
    }
}
