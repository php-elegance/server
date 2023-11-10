<?php

namespace Middleware;

use Closure;
use Elegance\Server\Response;

/** cros */
class MidCros
{
    function __invoke(Closure $next)
    {
        Response::header('Elegance-Cros', 'true');

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            Response::header('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
            Response::header('Access-Control-Allow-Credentials', 'true');
            Response::header('Access-Control-Max-Age', 86400);
        }

        if (IS_OPTIONS) {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                Response::header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                Response::header('Access-Control-Allow-Headers', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);

            Response::status(STS_OK);
            Response::send();
        }

        return $next();
    }
}
