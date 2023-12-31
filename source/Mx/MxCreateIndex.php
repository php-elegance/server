<?php

namespace Mx;

use Elegance\Core\File;
use Elegance\Core\Import;

class MxCreateIndex extends Mx
{
    function __invoke()
    {
        if (!File::check('index.php')) {
            File::create('index.php', Import::content('#elegance-server/view/template/mx/index.txt'));
            self::echo('Arquivo index.php criado');
        }
    }
}
