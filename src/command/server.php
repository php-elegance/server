<?php

// php mx server

use Elegance\Core\Terminal;

return function () {
    $port = env('PORT');

    Terminal::run('install.index');

    Terminal::echo('-------------------------------------------------');
    Terminal::echo('| Iniciando servidor PHP');
    Terminal::echo('| Acesse: [#]', "http://127.0.0.1:$port/");
    Terminal::echo('| Use: [#] para finalizar o servidor', "CLTR + C");
    Terminal::echo("| Escutando porta [#]", $port);
    Terminal::echo('-------------------------------------------------');
    Terminal::echo('');

    echo shell_exec("php -S 127.0.0.1:$port index.php");
};
