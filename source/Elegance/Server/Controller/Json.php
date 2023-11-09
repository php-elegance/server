<?php

namespace Elegance\Server\Controller;

use Closure;
use Error;
use Exception;

class Json
{
    function encaps(Closure $action)
    {
        try {
            $response = $action;
            
        } catch (Error | Exception $e) {
        }
    }
}
