<?php

namespace SuStartX\JWTRedisMultiAuth\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     *
     * @return JsonResponse
     */
    public function handle($request, Closure $next)
    {
        $request = $this->setIfClaimIsNotExist($request);

        if (config('jwt_redis_multi_auth.check_banned_user')) {
            $this->setAuthedUser($request);

            if (!$request->authedUser->checkUserStatus()) {
                return $this->getErrorResponse('AccountBlockedException', Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}

