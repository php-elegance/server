<?php

namespace Elegance\Server\Server;

// php mx install.index

return function () {
    $base = path('#elegance-server/view/template/mx/index.txt');

    File::create('index.php', Import::content($base));

    Terminal::echo('Arquivo index instalado.');
};
