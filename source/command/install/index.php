<?php

namespace Elegance;

// php mx install.index

return function () {
    $base = path('#elegance-server/source/template/mx/index.txt');

    File::create('index.php', Import::content($base));

    Terminal::echo('Arquivo index instalado.');
};
