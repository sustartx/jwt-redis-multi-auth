<?php

namespace SuStartX\JWTRedisMultiAuth\Factories;

use Illuminate\Foundation\Auth\User as Authenticatable;
use SuStartX\JWTRedisMultiAuth\Contracts\DataFactoryContract;

class BaseUserDataFactory implements DataFactoryContract
{
    public function data(Authenticatable $authenticatable)
    {
        return [
            'name' => $authenticatable->name,
            'email' => $authenticatable->email,
        ];
    }
}
