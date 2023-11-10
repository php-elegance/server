<?php

namespace Mx;

use Elegance\Core\File;
use Elegance\Core\Import;
use Exception;

class MxCreateMiddleware extends Mx
{
    function __invoke($name)
    {
        $middleware = strtolower($name);
        $middleware = str_replace('.', '/', $middleware);
        $middleware = path("$middleware.php");

        $class = explode('.', $name);
        $class = array_map(fn ($v) => ucfirst($v), $class);
        $class = implode("", $class);
        $class = "Mid$class";

        $file = "source/Middleware/$class.php";

        if (File::check($file))
            throw new Exception("Middleware [$name] already exists");

        $template = path("#elegance-server/view/template/mx/middleware.txt");

        $template = Import::content($template);

        $template = prepare($template, [
            'name' => $name,
            'class' => $class
        ]);

        File::create($file, $template);

        self::echo('Middleware [[#]] criada com sucesso.', $name);
    }
}
