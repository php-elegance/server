<?php

// php mx create.structure

use Elegance\Core\Dir;
use Elegance\Core\File;
use Elegance\Core\Import;
use Elegance\Core\Terminal;

return function () {
    Dir::create("class");
    Dir::create("library");
    Dir::create("library/assets");
    Dir::create("src");
    Dir::create("src/helper");
    Dir::create("src/helper/constant");
    Dir::create("src/helper/function");
    Dir::create("src/helper/script");
    Dir::create("view");
    Terminal::echo('Estrutura de pastas criada');

    File::create('index.php', Import::content('#elegance-server/view/template/mx/index.txt'));
    Terminal::echo('Arquivo index.php criado');

    File::create('routes.php', Import::content('#elegance-server/view/template/mx/routes.txt'));
    Terminal::echo('Arquivo routes.php criado');
};
