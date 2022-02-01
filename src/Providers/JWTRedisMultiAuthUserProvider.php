<?php

namespace SuStartX\JWTRedisMultiAuth\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use SuStartX\JWTRedisMultiAuth\Contracts\DataFactoryContract;

class JWTRedisMultiAuthUserProvider extends EloquentUserProvider implements UserProviderContract
{
    protected DataFactoryContract $data_factory;

    public function __construct(HasherContract $hasher, $model, DataFactoryContract $data_factory)
    {
        parent::__construct($hasher, $model);
        $this->data_factory = $data_factory;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return;
        }

        $query = $this->newModelQuery()->with(config('jwtredismultiauth.cache_relations'));

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function getDataFactory(){
        return $this->data_factory;
    }
}
