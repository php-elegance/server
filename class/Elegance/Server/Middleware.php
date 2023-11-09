<?php

namespace Elegance\Server;

use Closure;
use Elegance\Core\File;
use Elegance\Core\Import;
use Elegance\Core\Path;
use Error;

abstract class Middleware
{
    protected static array $queue = [];

    protected static array $registred = [];

    /** Registra uma middleware para ser chamada via string */
    static function register(string $name, array|string|Closure $middleware)
    {
        self::$registred[$name] = $middleware;
    }

    /** Executa uma fila de middlewares retornando a action */
    static function run(array $queue, $action)
    {
        if (!is_closure($action))
            $action = fn () => $action;

        $queue[] = $action;

        return self::execute($queue);
    }

    protected static function execute(mixed &$queue): mixed
    {
        if (count($queue)) {
            $middleware = array_shift($queue);
            $middleware = self::getCallable($middleware);
            $next = fn () => self::execute($queue);
            return $middleware($next) ?? $next();
        }

        return null;
    }

    protected static function getCallable(mixed $middleware)
    {
        if (is_array($middleware))
            return fn ($next) => self::run([...$middleware], $next);

        if (is_string($middleware)) {

            if (isset(self::$registred[$middleware]))
                return self::getCallable(self::$registred[$middleware]);

            $middleware = str_replace('.', '/', $middleware);

            $alias = Path::getAlias();
            $alias = array_keys($alias);

            $path = path(strtolower("src/middleware/$middleware.php"));

            while (!File::check($path) && count($alias))
                $path = path(array_pop($alias), strtolower("src/middleware/$middleware.php"));

            if (!File::check($path))
                throw new Error("Middleware [$middleware] not found");

            $middleware = Import::return($path);

            return self::getCallable($middleware);
        }

        if (is_closure($middleware))
            return $middleware;

        if (is_null($middleware))
            return fn ($next) => $next();

        throw new Error('Impossible middleware resolve');
    }
}
