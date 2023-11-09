<?php

namespace Elegance\Server\Controller;

use Elegance\Server\Request;

class Prefix
{
    function redirect(string $response): void
    {
        redirect($response);
    }

    function redirectWithRequestParameters(string $response): void
    {
        redirect($response, ...Request::route());
    }

    function show(string $response): string
    {
        return prepare($response, Request::route());
    }
}
