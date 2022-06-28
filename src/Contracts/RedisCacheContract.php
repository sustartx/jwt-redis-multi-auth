<?php

namespace SuStartX\JWTRedisMultiAuth\Contracts;

interface RedisCacheContract
{
    public function key(string $key): self;

    public function data($data): self;

    public function removeCache();

    public function getCache();

    public function refreshCache();

    public function cache();
}
