<?php

namespace Elegance\Server\Controller;

use Elegance\Server\Response;
use Error;
use Exception;

class Status
{
    /** Mensagem de erro generica */
    function error(Error|Exception $e)
    {
        if ($e->getCode() == STS_REDIRECT)
            $this->redirect($e);

        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        $content = $e->getMessage();

        if (empty($content))
            $content = env("STM_$status", null);

        Response::status($status);
        Response::content($content);
        Response::cache(false);

        Response::send();
    }

    /** Redirecionamento */
    function redirect(Error|Exception $e)
    {
        Response::header('location', $e->getMessage());
        Response::status(STS_REDIRECT);
        Response::send();
    }
}
