<?php

namespace Elegance\Server\Server;

// php mx middleware

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