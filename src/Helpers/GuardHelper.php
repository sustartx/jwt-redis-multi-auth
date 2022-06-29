<?php
namespace SuStartX\JWTRedisMultiAuth\Helpers;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Token;

class GuardHelper
{
    public static function autoDetectGuard(){
        $request = request();

        $prefix = config('jwt_redis_multi_auth.guard_prefix');
        $default_guard_name = config('auth.defaults.guard');

        // ----------------------------------------------------------------------------------------------------
        // Guard ismi request içinden tespit edilmeye çalışılıyor..
        // ----------------------------------------------------------------------------------------------------
        // Request içinde izin verilen key değerlerinden birisi varsa guard değeri request içinden tespit edilir
        $login_type_guard_input_names = config('jwt_redis_multi_auth.login_type_guard_input_names');
        $request_guard_name = null;
        foreach ($login_type_guard_input_names as $login_type_guard_input_name) {
            if ($request->has($login_type_guard_input_name) && $request->get($login_type_guard_input_name) != '') {
                $request_guard_name = $prefix . $request->get($login_type_guard_input_name);
                break;
            }
        }
        // ----------------------------------------------------------------------------------------------------

        // Giriş yapmak istiyorsa..
        if(config('jwt_redis_multi_auth.login_route_name') === $request->route()->getName()){
            if (!is_null($request_guard_name)){
                $guard_name = $request_guard_name;
            }else{
                $guard_name = $default_guard_name;
            }
        }else{
            $token_cookie = \Cookie::get(env('COOKIE_NAME'));
            $token_bearer = request()->bearerToken();
            $token = $token_cookie ?: $token_bearer ?: null;

            if ($token){
                $decoded_token = app()->get('tymon.jwt.manager')->decode(new Token($token));
                $guard = $decoded_token->get(config('jwt_redis_multi_auth.jwt_guard_key'));
                // Oturum varsa oturumdan hangi guard ile çalıştığı tespit edildi
                $guard_name = $prefix . $guard;
            }else{
                if (!is_null($request_guard_name)){
                    $guard_name = $request_guard_name;
                }else{
                    $guard_name = $default_guard_name;
                }
            }
        }

        return $guard_name;
    }
}
