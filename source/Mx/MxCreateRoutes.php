<?php

namespace Mx;

use Elegance\Core\File;
use Elegance\Core\Import;

class MxCreateRoutes extends Mx
{
    function __invoke()
    {
        File::create('routes.php', Import::content('#elegance-server/view/template/mx/routes.txt'));
        self::echo('Arquivo routes.php criado');
    }
}
