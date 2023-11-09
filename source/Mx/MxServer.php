<?php

namespace Mx;

class MxServer extends Mx
{
    function __invoke()
    {
        $port = env('PORT');

        self::run('install.index');

        self::echo('-------------------------------------------------');
        self::echo('| Iniciando servidor PHP');
        self::echo('| Acesse: [#]', "http://127.0.0.1:$port/");
        self::echo('| Use: [#] para finalizar o servidor', "CLTR + C");
        self::echo("| Escutando porta [#]", $port);
        self::echo('-------------------------------------------------');
        self::echo('');

        echo shell_exec("php -S 127.0.0.1:$port index.php");
    }
}
