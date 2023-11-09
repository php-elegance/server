<?php

namespace Controller;

use Elegance\Request;

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

    protected function show(string $response): string
    {
        return prepare($response, Request::route());
    }
}
