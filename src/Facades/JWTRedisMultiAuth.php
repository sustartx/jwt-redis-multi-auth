<?php

namespace SuStartX\JWTRedisMultiAuth\Facades;

use Illuminate\Support\Facades\Facade;

class JWTRedisMultiAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'JWTRedisMultiAuth';
    }
}
