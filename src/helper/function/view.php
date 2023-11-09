<?php

use Elegance\Server\View;

if (!function_exists('view')) {

    /** Renderiza uma view baseando em uma referencia de arquivo */
    function view(string $ref, ?array $data = []): string
    {
        return View::renderFile($ref, $data);
    }
}
