<?php

namespace SuStartX\JWTRedisMultiAuth\Contracts;

use Illuminate\Foundation\Auth\User as Authenticatable;

interface DataFactoryContract
{
    public function data(Authenticatable $authenticatable);
}
