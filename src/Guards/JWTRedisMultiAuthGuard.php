<?php

namespace SuStartX\JWTRedisMultiAuth\Guards;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWT;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;
use SuStartX\JWTRedisMultiAuth\Facades\RedisCache;

class JWTRedisMultiAuthGuard extends JWTGuard
{
    private $config = [];

    protected $lastAttempted = null;

    public function __construct(JWT $jwt, UserProvider $provider, Request $request, Dispatcher $eventDispatcher, $config)
    {
        parent::__construct($jwt, $provider, $request, $eventDispatcher);

        $this->config = $config;
    }

    public function getConfig($key = null){
        if(is_null($key)){
            return $this->config;
        }
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return Authenticatable
     */
    public function setLastAttempted(Authenticatable $authenticatable)
    {
        $this->lastAttempted = $authenticatable;
    }

    /**
     * UYARI !!! - Kullanıcının durumuna göre dönüş değerini değiştirecek geliştirme yaptım.
     *
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param bool $login
     *
     * @return array
     */
    public function attempt(array $credentials = [], $login = true, $data_factory = null)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        $prefix = config('jwt_redis_multi_auth.guard_prefix');

        $this->lastAttempted->addCustomClaims([
            config('jwt_redis_multi_auth.jwt_guard_key') => str_replace($prefix, '', $this->getConfig('provider')),
        ]);

        if ($data_factory === null && config('jwt_redis_multi_auth.disable_default_user_data_factory') === false){
            $data_factory = auth()->guard()->getProvider()->getDataFactory();
        }

        if($data_factory){
            $this->lastAttempted->addCustomClaims([
                'user' => $data_factory->data($this->lastAttempted)
            ]);
        }


        $result_type = 'SUCCESS';
        $status = false;
        $token = null;

        // E-posta adresini doğrulamış mı?
        if(!$this->lastAttempted->hasVerifiedEmail()){
            $result_type = 'EMAIL_NOT_VERIFIED';
        }

        // Yasaklanmış mı?
        if ($this->lastAttempted->is_banned){
            $result_type = 'BANNED';
        }

        $this->fireAttemptEvent($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if (config('jwt_redis_multi_auth.check_banned_user')) {
                if (!$user->checkUserStatus()) {
                    throw new AuthorizationException('Your account has been blocked by the administrator.');
                }
            }

            $this->refreshAuthFromRedis($user);

            if ($login){
                $token = $this->login($user);

                $this->setUser($this->lastAttempted);
                $this->storeRedis(true);
            }else{
                $token = true;
            }

            $status = true;
        }

        $this->fireFailedEvent($user, $credentials);

        return [
            'status' => $status,
            'type' => $result_type,
            'token' => $token,
        ];
    }

    public function refreshAuthFromRedis($user)
    {
        return RedisCache::key($user->getRedisKey())->data($user)->refreshCache();
    }

    /**
     * @OVERRIDE!
     *
     * Get the currently authenticated user.
     *
     * !Important; Made some changes this method for check authed user without db query.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return $this->user ?? $this->retreiveByRedis();
    }

    /**
     * @return mixed
     */
    public function retreiveByRedis()
    {
        return $this->request->authedUser ?? $this->getOrSetToRedis();
    }

    /**
     * @return mixed
     */
    public function getOrSetToRedis()
    {
        return $this->getAuthFromRedis() ?? $this->setAuthToRedis();
    }

    /**
     * @return mixed
     */
    public function getAuthFromRedis()
    {
        return RedisCache::key($this->getRedisKeyFromClaim())->getCache();
    }

    public function setAuthToRedis()
    {
        if ($this->request->bearerToken()) {
            // TODO : Veritabanından kontrol edilmeli, girişine engel herhangi bir durum yoksa yeni veriler alınarak redis güncellenmeli
            return $this->storeRedis();
        }

        // If token not found, we need to return null.
        // Because Laravel's need this user object even if empty.
        return null;
    }

    public function removeAuthFromRedis()
    {
        return RedisCache::key($this->getRedisKeyFromClaim())->removeCache();
    }

    /**
     * @OVERRIDE!
     *
     * Log a user into the application using their credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            $this->storeRedis(true);

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRedisKeyFromClaim()
    {
        return $this->request->jwt_guard_key . '_' . $this->request->claim;
    }

    /**
     * @param bool $login
     *
     * @return mixed
     */
    public function storeRedis($login = false)
    {
        // Giriş yapmaya çalışıyorsa $this->lastAttempt içindeki key değeri alınıyor.
        if($login)
        {
            return RedisCache::key($this->lastAttempted->getRedisKey())->data($this->lastAttempted)->cache();
        }
        else
        // Giriş dışında kayıt gerekiyorsa JWT içindeki key değeri alınıyor.
        {
            return RedisCache::key($this->getRedisKeyFromClaim())->data(JWTAuth::parseToken()->authenticate()->load(config('jwt_redis_multi_auth.cache_relations')))->cache();
        }
    }
}
