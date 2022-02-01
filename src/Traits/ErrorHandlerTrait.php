<?php

namespace SuStartX\JWTRedisMultiAuth\Traits;

use Throwable;

trait ErrorHandlerTrait
{
    public function renderForJson($request, Throwable $throwable)
    {
        $json = parent::render($request, $throwable);
        if($json instanceof \Symfony\Component\HttpFoundation\JsonResponse){
            $data = $json->getData(true);
            $data['status'] = $json->getStatusCode();
            $data['errors'] = [\Illuminate\Support\Arr::get($data, 'exception', 'Something went wrong!')];
            $data['message'] = \Illuminate\Support\Arr::get($data, 'message', '');
            $json->setData($data);
        }

        $exception = $this->prepareException($throwable);

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                break;
            case 403:
                $response['message'] = 'Forbidden';
                break;
            case 404:
                $response['message'] = 'Not Found';
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                break;
            case 422:
            case 429:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }

//        if (config('app.debug')) {
//            $response['trace'] = $exception->getTrace();
//            $response['code'] = $exception->getCode();
//        }

        if (config('app.debug')) {
            $response = [
                'message' => $throwable->getMessage(),
                'exception' => get_class($throwable),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => collect($throwable->getTrace())->map(function ($trace) {
                    return \Arr::except($trace, ['args']);
                })->all(),
            ];
        }else{
            if(!$this->isHttpException($throwable)){
                $response['message'] = 'Server Error';
            }
        }

        $response['status'] = $statusCode;

        return response()->json($response, $statusCode);
    }
}
