<?php

namespace SuStartX\JWTRedisMultiAuth\Traits;

use Throwable;

trait ErrorHandlerTrait
{
    public function responseFromJWTRedisMultiAuth($request, Throwable $throwable){
        $exception_groups = config('jwt_redis_multi_auth.exception_groups');
        foreach ($exception_groups as $exception_group) {
            foreach ($exception_group as $exception => $value) {
                if ($throwable instanceof $exception){
                    $data = [
                        'title' => $value['title'],
                        'message' => (strlen($value['message'])) ? $value['message']: $throwable->getMessage(),
                        'status' => $value['status'],
                    ];
                    return response()->json($data,$value['http_code']);
                }
            }
        }
    }
}
