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

        self::run('create.index');

        self::run('create.routes');
    }
}
