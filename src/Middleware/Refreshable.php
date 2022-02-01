<?php

namespace SuStartX\JWTRedisMultiAuth\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Manager;
use PHPOpenSourceSaver\JWTAuth\Token;
use Symfony\Component\HttpFoundation\Response;

class Refreshable extends BaseMiddleware
{
    protected $auth;

    protected $manager;

    public function __construct(JWTAuth $auth, Manager $manager)
    {
        $this->auth = $auth;
        $this->manager = $manager;
    }

    public function handle($request, Closure $next)
    {
        $this->checkForToken($request, $next);

        try {
            $token = $this->auth->parseToken()->refresh();

            $request->claim = $this->manager->decode(new Token($token))->get('sub');
        } catch (TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e, Response::HTTP_UNAUTHORIZED);
        }

        return $this->setAuthenticationResponse($token);
    }

    protected function checkForToken(Request $request, Closure $next)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            return $this->getErrorResponse('TokenNotProvided', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    protected function setAuthenticationResponse($token = null)
    {
        $token = $token ?: $this->auth->refresh();

        return response()->json(['token' => $token]);
    }
}
