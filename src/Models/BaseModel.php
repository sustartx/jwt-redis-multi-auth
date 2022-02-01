<?php

namespace SuStartX\JWTRedisMultiAuth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseModel extends Authenticatable
{
    function __construct()
    {
        parent::__construct();
    }
}
