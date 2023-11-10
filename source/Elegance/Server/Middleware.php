<?php

namespace Elegance\Server;

use Closure;
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

            $class = explode('.', $middleware);
            $class = array_map(fn ($v) => ucfirst($v), $class);
            $class = implode("", $class);
            $class = "\\Middleware\\Mid$class";

            if (class_exists($class))
                return fn ($next) => (new $class)($next);

            throw new Error("Middleware [$middleware] not found");
        }

        if (is_closure($middleware))
            return $middleware;

        if (is_null($middleware))
            return fn ($next) => $next();

        throw new Error('Impossible middleware resolve');
    }
}
