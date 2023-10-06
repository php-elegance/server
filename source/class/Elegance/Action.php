<?php

namespace Elegance;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

abstract class Action
{
    protected static array $prefix = [];

    /** Retorna o resultado de uma chamada de ação */
    static function run($call, array $data = [])
    {
        $action = self::get($call, $data);

        return $action();
    }

    /** Define uma ação para chamadas de ação do tipo string com prefixos determinados */
    static function prefix(string $prefix, Closure $action)
    {
        self::$prefix[$prefix] = $action;
    }

    /** Retorna uma função de execução de uma chamade de ação */
    static function get($response, array $data = []): Closure
    {
        if (is_string($response)) {
            uksort(self::$prefix, fn ($a, $b) => strlen($b) <=> strlen($a));
            foreach (self::$prefix as $prefix => $action)
                if (str_starts_with($response, $prefix))
                    return fn () => $action(substr($response, strlen($prefix)), $data);
        } else {
            if (is_httpStatus($response))
                return fn () => throw new Exception('', $response);

            if (is_array($response))
                return fn () => json_encode($response);

            if (is_closure($response))
                return fn () => self::action_closure($response, $data);
        }
        return fn () => throw new Exception('Incorrect call action', STS_INTERNAL_SERVER_ERROR);
    }

    /** Ação de execução de um objeto closure */
    protected static function action_closure(mixed $action, array $data = [])
    {
        $reflection = $action instanceof Closure ? new ReflectionFunction($action) : new ReflectionMethod($action, '__invoke');

        $params = [];
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            if (isset($data[$name])) {
                $params[] = $data[$name];
            } else if ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new Exception("Parameter [$name] is required", STS_INTERNAL_SERVER_ERROR);
            }
        }

        return $action(...$params) ?? STS_OK;
    }
}
