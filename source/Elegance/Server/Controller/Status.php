<?php

namespace Elegance\Server\Controller;

use Elegance\Server\Response;
use Error;
use Exception;

class Status
{
    /** Redirecionamento */
    function redirect(Error|Exception $e)
    {
        Response::header('location', $e->getMessage());
        Response::status(STS_REDIRECT);
        Response::send();
    }

    /** Mensagem de erro generica */
    function error(Error|Exception $e)
    {
        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        Response::status($status);
        Response::content($e->getMessage());
        Response::cache(false);
        Response::send();
    }
}
