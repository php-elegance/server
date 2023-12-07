<?php

namespace Mx;

class MxServer extends Mx
{
    function __invoke()
    {
        $port = env('PORT');

        self::run('create.index');

        self::run('create.routes');

        self::echo('-------------------------------------------------');
        self::echo('| Iniciando servidor PHP');
        self::echo('| Acesse: [#]', "http://localhost:$port/");
        self::echo('| Use: [#] para finalizar o servidor', "CLTR + C");
        self::echo("| Escutando porta [#]", $port);
        self::echo('-------------------------------------------------');
        self::echo('');

        echo shell_exec("php -S localhost:$port index.php");
    }
}
