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
            $this->encaps($next());
        } catch (Error | Exception $e) {
            $this->encapsCatch($e);
        }

        Response::type('json');
        Response::send();
    }

    /** Encapsula um conteúdo para dentro da resposta em uma estrutura JSON */
    protected function encaps($content)
    {
        if (is_httpStatus($content))
            throw new Exception('', $content);

        $status = Response::getStatus();

        Response::content($content, false);

        $content = $content ?? Response::getContent();

        $content = is_json($content) ? json_decode($content) : $content;

        $status = is_httpStatus($status) ? $status : STS_OK;

        $content = [
            'info' => [
                'elegance' => true,
                'status' => $status,
                'error' => $status > 399,
                'message' => env("STM_$status", null),
            ],
            'data' => $content
        ];

        Response::status($status);
        Response::content($content);
    }

    /** Encapsula um Error ou Exception para dentro da resposta em uma estrutura JSON */
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
}
