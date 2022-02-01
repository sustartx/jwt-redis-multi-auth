<?php

namespace SuStartX\JWTRedisMultiAuth\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpFoundation\Response;

class RoleOrPermissionMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
//        \DB::enableQueryLog();

        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e, Response::HTTP_UNAUTHORIZED);
        }

        $this->setAuthedUser($request);

        $rolesOrPermissions = is_array($roleOrPermission) ? $roleOrPermission : explode('|', $roleOrPermission);

        if (!$request->authedUser->hasAnyRole($rolesOrPermissions) && !$request->authedUser->hasAnyPermission($rolesOrPermissions)) {
            return $this->getErrorResponse('RoleOrPermissionException', Response::HTTP_FORBIDDEN);
        }

//        $query = \DB::getQueryLog();
//        \DB::disableQueryLog();

//        dd($query);

        return $next($request);
    }
}
