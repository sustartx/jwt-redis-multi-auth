<?php

namespace SuStartX\JWTRedisMultiAuth\Providers;

use Illuminate\Support\ServiceProvider;
use SuStartX\JWTRedisMultiAuth\Cache\RedisCache;
use SuStartX\JWTRedisMultiAuth\Commands\TestCommand;
use SuStartX\JWTRedisMultiAuth\Contracts\DataFactoryContract;
use SuStartX\JWTRedisMultiAuth\Contracts\RedisCacheContract;
use SuStartX\JWTRedisMultiAuth\Factories\BaseUserDataFactory;
use SuStartX\JWTRedisMultiAuth\Guards\JWTRedisMultiAuthGuard;
use SuStartX\JWTRedisMultiAuth\Hashers\JWTRedisMultiAuthHasher;

class JWTRedisMultiAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'jwtredismultiauth');
        $this->app->register(EventServiceProvider::class);

        $this->app->bind(RedisCacheContract::class, function ($app) {
            return new RedisCache();
        });

        $this->app->bind(DataFactoryContract::class, function ($app) {
            return new BaseUserDataFactory(app(auth()->guard()->getProvider()->getModel()));
        });
    }

    public function boot()
    {
        \Auth::extend('JWTRedisMultiAuthGuard', function($app, $guard_name, $config){
            $jwt = $app['tymon.jwt'];
            $provider = \Auth::createUserProvider($config['provider']);
            $request = $app['request'];
            $dispatcher = $app['events'];

            return new JWTRedisMultiAuthGuard($jwt, $provider, $request, $dispatcher, $config);
        });
        \Auth::provider("JWTRedisMultiAuthProvider", function ($app, array $config) {
            $module_config = config('jwtredismultiauth');

            // Önce $config ile gelen verileri kontrol et..
            if(array_key_exists('hasher', $config)){
                // doğrudan geldi, önce değerlendir
                if($config['hasher'] === 'default' || $config['hasher'] === 'bcrypt'){
                    $hasher = $app['hash'];
                }else if ($config['hasher'] === 'jwtredismultiauth'){
                    $hasher = new JWTRedisMultiAuthHasher();
                }else{
                    $hasher = new $config['hasher'];
                }
            }else{
                // $config ile hash bilgisi gelmediyse varsayılan modül ayarlarına bak..
                if($module_config['hasher'] === 'default' || $module_config['hasher'] === 'bcrypt'){
                    $hasher = $app['hash'];
                }else if($module_config['hasher'] === 'jwtredismultiauth'){
                    $hasher = new JWTRedisMultiAuthHasher();
                }else{
                    $hasher = new $module_config['hasher'];
                }
            }

            // Data factory hazırlanıyor
            if(array_key_exists('data_factory', $config)){
                $data_factory = app($config['data_factory']);
            }else{
                $data_factory = new BaseUserDataFactory();
            }

            return new JWTRedisMultiAuthUserProvider($hasher, $config['model'], $data_factory);
        });

        $this->app->bind('JWTRedisMultiAuthHasher',function(){
            $config = config('hashing.bcrypt');
            return new JWTRedisMultiAuthHasher($config);
        });

//        $authenticatable_models = get_user_authenticatable_models();
//        foreach ($authenticatable_models as $model) {
//            $model = app()->make($model);
//            if ($model instanceof \SuStartX\JWTRedisMultiAuth\Models\JWTRedisMultiAuthAuthenticatableBaseModel){
//                $model::observe(config('jwtredismultiauth.observer'));
//            }
//        }

        if ($this->app->runningInConsole()) {
            $this->commands([
//                TestCommand::class
            ]);

            $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'jwtredismultiauth');
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('jwtredismultiauth.php'),
            ], 'config');
        }
    }
}
