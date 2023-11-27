<?php

namespace Middleware;

use Closure;
use Elegance\Server\Response;
use Error;
use Exception;

/** encaps */
class MidEncaps
{
    final function __invoke(Closure $next): never
    {
        try {
            $this->default($next());
        } catch (Error | Exception $e) {
            $status = $e->getCode();
            if (method_exists($this, "catch_$status")) {
                $this->{"catch_$status"}($e);
            } else {
                $this->catch($e);
            }
        }
        Response::send();
    }

    protected function default($content)
    {
        if (is_httpStatus($content)) throw new Exception('', $content);

        $status = Response::getStatus();
        $status = is_httpStatus($status) ? $status : STS_OK;

        Response::content($content, false);
        $content = $content ?? Response::getContent();

        $content = is_json($content) ? json_decode($content) : $content;

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

    protected function catch(Error|Exception $e)
    {
        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        $message = $e->getMessage();

        $message = empty($message) ? env("STM_$status", null) : $message;
        $message = is_json($message) ? json_decode($message, true) : ['message' => $message];

        $response = [
            'info' => [
                'elegance' => true,
                'status' => $status,
                'error' => $status > 399,
                ...$message
            ],
            'data' => null
        ];

        Response::header('Error-Message', remove_accents($response['info']['message']));
        Response::header('Error-Status', $response['info']['status']);

        if (env('DEV') && $response['info']['error']) {
            $response['info']['file'] = $e->getFile();
            $response['info']['line'] = $e->getLine();
            Response::header('Error-File', $response['info']['file']);
            Response::header('Error-Line', $response['info']['line']);
        }

        Response::status($status);
        Response::cache(false);
        Response::content($response);
    }

    protected function catch_303(Error|Exception $e)
    {
        Response::header('location', $e->getMessage());
        Response::status(STS_REDIRECT);
    }
}
