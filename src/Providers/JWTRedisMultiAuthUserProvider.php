<?php

namespace SuStartX\JWTRedisMultiAuth\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Support\Arrayable;
use SuStartX\JWTRedisMultiAuth\Contracts\DataFactoryContract;
use Closure;

class JWTRedisMultiAuthUserProvider extends EloquentUserProvider implements UserProviderContract
{
    protected DataFactoryContract $data_factory;

    public function __construct(HasherContract $hasher, $model, DataFactoryContract $data_factory)
    {
        parent::__construct($hasher, $model);
        $this->data_factory = $data_factory;
    }

    /**
     * NOT : Bu kod "laravel/framework/src/Illuminate/Auth/EloquentUserProvider.php" içinden alındı ve sadece sorguya with() eklendi.
     */
    /**
     * @OVERRIDE!
     *
     * Retrieve a user by the given credentials.
     *
     * !Important; I made some changes this method for eager loading user roles&permissions.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return;
        }

        $query = $this->newModelQuery()
            ->with(config('jwt_redis_multi_auth.cache_relations'))
        ;

        foreach ($credentials as $key => $value) {
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
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
