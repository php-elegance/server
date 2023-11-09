<?php

// php mx middleware

use Elegance\Core\File;
use Elegance\Core\Import;
use Elegance\Core\Terminal;

return function ($name) {
    $middleware = strtolower($name);
    $middleware = str_replace('.', '/', $middleware);
    $middleware = path("$middleware.php");

    $template = path("#elegance-server/view/template/mx/middleware.txt");
    $template = Import::content($template);
    $template = prepare($template, ['name' => $name]);

    File::create("src/middleware/$middleware", $template);

    Terminal::echo('Middleware [[#]] criada com sucesso.', $name);
};
