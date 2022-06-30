<?php

namespace SuStartX\JWTRedisMultiAuth\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SuStartX\JWTRedisMultiAuth\Response\JWTRedisMultiAuthSuccessResponse;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Manager;
use PHPOpenSourceSaver\JWTAuth\Token;

class Refreshable extends BaseMiddleware
{
    /**
     * The JWT Authenticator.
     *
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param JWTAuth $auth
     *
     * @return void
     */
    public function __construct(JWTAuth $auth, Manager $manager)
    {
        $this->auth = $auth;
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     *
     * @return JsonResponse|Response
     */
    public function handle($request, Closure $next)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            return $this->getErrorResponse('TokenNotProvided', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $token = $this->auth->parseToken()->refresh();
            $request->claim = $this->manager->decode(new Token($token))->get('sub');
        } catch (TokenInvalidException | JWTException $exception) {
            return $this->getErrorResponse($exception, Response::HTTP_UNAUTHORIZED);
        }

        $token = $token ?: $this->auth->refresh();

        return response()->json(new JWTRedisMultiAuthSuccessResponse(
            200,
            [
                'token' => $token
            ]
        ), 200);
    }
}
