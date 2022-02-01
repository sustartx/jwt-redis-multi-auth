<?php

namespace SuStartX\JWTRedisMultiAuth\Middleware;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class BaseMiddleware
{
    protected function setIfClaimIsNotExist($request)
    {
        /**
         *  If you don't use Authentication Middleware before that Middleware,
         *  application need to set a Claim (by Token) in Request object for
         *  using Laravel's Auth facade.
         */
        if ($request->claim === null) {
            $token = JWTAuth::getPayload(JWTAuth::getToken());
            $request->claim = $token->get('sub');
        }

        return true;
    }

    protected function setAuthedUser($request)
    {
        $request->authedUser = JWTAuth::parseToken()->toUser();
    }

    protected function getErrorResponse($exception, $status = 200)
    {
        $error = config('jwtredismultiauth.errors.'.class_basename($exception)) ?? config('jwtredismultiauth.errors.default');
        return response()->json([
            'status' => false,
            'title' => $error['title'],
            'message' => $error['message'],
            'code' => $error['code'],
        ], $status);
    }
}
