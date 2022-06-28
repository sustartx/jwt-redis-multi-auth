<?php

namespace SuStartX\JWTRedisMultiAuth\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $permission
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e, Response::HTTP_UNAUTHORIZED);
        }

        $this->setAuthedUser($request);

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        if (config('jwt_redis_multi_auth.check_banned_user')) {
            if (!$request->authedUser->checkUserStatus()) {
                // TODO : Buraya yetkisiz başka kod girilebilir
                return $this->getErrorResponse('AccountBlockedException', Response::HTTP_UNAUTHORIZED);
            }
        }

        foreach ($permissions as $permission) {
            if ($request->authedUser->can($permission)) {
                return $next($request);
            }
        }

        return $this->getErrorResponse('PermissionException', Response::HTTP_FORBIDDEN);
    }
}
