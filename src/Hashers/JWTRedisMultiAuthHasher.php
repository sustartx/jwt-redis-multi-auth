<?php

namespace SuStartX\JWTRedisMultiAuth\Hashers;

use Illuminate\Hashing\BcryptHasher;

class JWTRedisMultiAuthHasher extends BcryptHasher
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }
}
