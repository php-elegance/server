<?php

namespace Mx;

use Elegance\Core\Dir;
use Elegance\Core\File;
use Elegance\Core\Import;

class MxCreateStructure extends Mx
{
    function __invoke()
    {
        Dir::create("helper");
        Dir::create("helper/constant");
        Dir::create("helper/function");
        Dir::create("helper/script");
        Dir::create("library");
        Dir::create("library/assets");
        Dir::create("source");
        Dir::create("view");
        self::echo('Estrutura de pastas criada');

        File::create('index.php', Import::content('#elegance-server/view/template/mx/index.txt'));
        self::echo('Arquivo index.php criado');

        File::create('routes.php', Import::content('#elegance-server/view/template/mx/routes.txt'));
        self::echo('Arquivo routes.php criado');
    }
}
