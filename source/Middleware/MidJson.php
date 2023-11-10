<?php

namespace Middleware;

use Closure;
use Elegance\Server\Response;
use Error;
use Exception;

/** json */
class MidJson
{
    function __invoke(Closure $next)
    {
        try {
            $response = $next();

            if (is_httpStatus($response))
                throw new Exception('', $response);

            $this->encapsResponse($response);
        } catch (Exception | Error $e) {
            $this->encapsCatch($e);
        }

        Response::send();
    }

    /** Encapsula um erro ou exception dentro de um json de resposta APIs */
    protected function encapsCatch(Error | Exception $e)
    {
        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        $message = $e->getMessage();

        $response = [
            'info' => [
                'elegance' => true,
                'status' => $status,
                'error' => $status > 399,
            ],
            'data' => null
        ];

        if ($status == STS_REDIRECT) {
            $response['info']['location'] = !empty($message) ? url($message) : url('.');
            Response::header('location', $response['info']['location']);
            $message = null;
        }

        $message = empty($message) ? env("STM_$status", null) : $message;
        $message = is_json($message) ? json_decode($message, true) : ['message' => $message];

        $response['info'] = [
            ...$response['info'],
            ...$message
        ];

        if (env('DEV') && $response['info']['error']) {
            $response['info']['file'] = $e->getFile();
            $response['info']['line'] = $e->getLine();
            Response::header('Elegance-Error-File', $response['info']['file']);
            Response::header('Elegance-Error-Line', $response['info']['line']);
        }

        Response::status($status);
        Response::cache(false);
        Response::content($response);
    }

    /** Encapsula o conteúdo da resposta dentro de um json de resposta API */
    protected function encapsResponse($content)
    {
        $status = Response::getStatus();

        Response::content($content, false);

        $content = $content ?? Response::getContent();

        $content = is_json($content) ? json_decode($content) : $content;

        $status = is_httpStatus($status) ? $status : STS_OK;

        if (is_array($content)) {
            $content = [
                'info' => [
                    'elegance' => true,
                    'status' => $status,
                    'error' => $status > 399,
                    'message' => env("STM_$status", null),
                ],
                'data' => $content
            ];
        }

        Response::status($status);
        Response::content($content);
    }
}
