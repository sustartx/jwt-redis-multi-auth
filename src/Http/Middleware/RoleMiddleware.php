<?php

namespace SuStartX\JWTRedisMultiAuth\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware extends BaseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     *
     * @throws AuthorizationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e, Response::HTTP_UNAUTHORIZED);
        }

        $this->setAuthedUser($request);

        $roles = is_array($role) ? $role : explode('|', $role);

        if (config('jwt_redis_multi_auth.check_banned_user')) {
            if (!$request->authedUser->checkUserStatus()) {
                return $this->getErrorResponse('AccountBlockedException');
            }
        }

        if (!$request->authedUser->hasAnyRole($roles)) {
            return $this->getErrorResponse('RoleException', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
