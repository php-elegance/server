<?php

namespace Mx;

use Elegance\Core\File;
use Elegance\Core\Import;

class MxCreateMiddleware extends Mx
{
    function __invoke($name)
    {
        $middleware = strtolower($name);
        $middleware = str_replace('.', '/', $middleware);
        $middleware = path("$middleware.php");

        $template = path("#elegance-server/view/template/mx/middleware.txt");
        $template = Import::content($template);
        $template = prepare($template, ['name' => $name]);

        File::create("source/Middleware/$middleware", $template);

        self::echo('Middleware [[#]] criada com sucesso.', $name);
    }
}
