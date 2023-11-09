<?php

// php mx install.index

use Elegance\Core\File;
use Elegance\Core\Import;
use Elegance\Core\Terminal;

return function () {
    $base = path('#elegance-server/view/template/mx/index.txt');

    File::create('index.php', Import::content($base));

    Terminal::echo('Arquivo index instalado.');
};
