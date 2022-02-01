<?php

namespace SuStartX\JWTRedisMultiAuth\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\JWT;
use SuStartX\JWTRedisMultiAuth\Facades\RedisCache;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class JWTRedisMultiAuthGuard extends JWTGuard implements Guard
{
    private $config = [];

    public function __construct(JWT $jwt, UserProvider $provider, Request $request, Dispatcher $eventDispatcher, $config)
    {
        parent::__construct($jwt, $provider, $request, $eventDispatcher);
        $this->config = $config;
    }

    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            $this->storeRedis(true);

            return true;
        }

        return false;
    }

    public function user()
    {
        return $this->user ?? $this->retreiveByRedis();
    }

    public function attempt(array $credentials = [], $login = true)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {

            $this->refreshAuthFromRedis($user);

            return $login ? $this->login($user) : true;
        }

        return false;
    }

    public function retreiveByRedis()
    {
        return $this->request->authedUser ?? $this->getOrSetToRedis();
    }

    public function getOrSetToRedis()
    {
        return $this->getAuthFromRedis() ?? $this->setAuthToRedis();
    }

    public function getAuthFromRedis()
    {
        return RedisCache::key($this->getRedisKeyFromClaim())->getCache();
    }

    public function refreshAuthFromRedis($user)
    {
        return RedisCache::key($user->getRedisKey())->data($user)->refreshCache();
    }

    public function removeAuthFromRedis()
    {
        return RedisCache::key($this->getRedisKeyFromClaim())->removeCache();
    }

    public function getRedisKeyFromClaim()
    {
        return 'auth_'.$this->request->claim;
    }

    public function setAuthToRedis()
    {
        if ($this->request->bearerToken()) {
            return $this->storeRedis();
        }

        // If token not found, we need to return null.
        // Because Laravel's need this user object even if empty.
        return null;
    }

    public function storeRedis($login = false)
    {
        // If is Login value true, user cached from lastAttempt object.
        // else user cached from token in request object.
        if (!$login) {
            return RedisCache::key($this->getRedisKeyFromClaim())
                ->data(JWTAuth::parseToken()->authenticate()->load(config('jwtredismultiauth.cache_relations')))
                ->cache();
        }

        return RedisCache::key($this->lastAttempted->getRedisKey())->data($this->lastAttempted)->cache();
    }

    public function getConfig($key = null){
        if(is_null($key)){
            return $this->config;
        }
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }
}
