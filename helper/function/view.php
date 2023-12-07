<?php

use Elegance\Server\View;

if (!function_exists('view')) {

    /** Renderiza uma view baseando em uma referencia de arquivo */
    function view(string $ref, array|string $data = []): string
    {
        return View::render($ref, $data);
    }
}
