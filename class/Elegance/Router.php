<?php

namespace Elegance;

use Closure;
use Exception;

abstract class Router
{
    protected static array $route = [];
    protected static array $globalMiddleware = [];

    /** Adiciona rotas na lista de interpretação */
    static function add(array $middlewares, ?array $routes = null): void
    {
        if (is_null($routes)) {
            self::$globalMiddleware[] = [...self::$globalMiddleware, ...$middlewares];
        } else {
            foreach ($routes as $route => $response) {
                list($template, $params) = self::explodeRoute($route);
                self::$route[$template] = [
                    $params,
                    $response,
                    $middlewares
                ];
            }
        }
    }

    /** Resolve a requisição atual */
    static function solve()
    {
        Import::only("routes");

        $template = self::getTemplateMatch(self::$route);

        $route = !is_null($template) ? self::$route[$template] : [null, fn () => throw new Exception('Route not found', STS_NOT_FOUND), []];

        list($params, $response, $middleware) = $route;

        self::setParamnsData($template, $params);

        $action = Action::get($response, Request::route());

        $response = Middleware::run([...self::$globalMiddleware, ...$middleware], $action);

        Response::content($response);
        Response::send();
    }

    /** Limpa uma string para ser utilziada como uma rota */
    protected static function clearRoute($route)
    {
        if (strpos($route, '?') !== false) {
            $paramsQuery = explode('?', $route);
            $route = array_shift($paramsQuery);
            $paramsQuery = implode('&', $paramsQuery);
            $paramsQuery = explode('&', $paramsQuery);
            asort($paramsQuery);
        }

        $route = trim($route, '/');

        $route .= '/';

        $route = str_replace(['[...]', '+', '['], ['...', '/', '[#'], $route);
        $route = str_replace(['[##', '[#@', '[#='], ['[#', '[@', '[='], $route);

        $route = str_replace_all(['...', '.../', '......'], '/...', $route);
        $route = str_replace_all([' /', '//', '/ '], '/', $route);

        if ($paramsQuery ?? false)
            $route .= "?" . implode('?', $paramsQuery);

        return $route;
    }

    /** Separa o template dos parametros de uma rota */
    protected static function explodeRoute($route)
    {
        $params = [];

        $route = self::clearRoute($route);
        $route = explode('/', $route);

        foreach ($route as $pos => $param)
            if (str_starts_with($param, '[#')) {
                $route[$pos] = '#';
                $param = substr($param, 2, -1);
                if (strpos($param, ':')) {
                    $route[$pos] = substr($param, strpos($param, ':') + 1);
                    $param = substr($param, 0, strpos($param, ':'));
                }
                if (empty($param))
                    $param = null;
                $params[$pos] = $param;
            }
        $route = implode('/', $route);
        $params = empty($params) ? null : $params;
        return [$route, $params];
    }

    /** Organiza um array de rotas preparando para a interpretação */
    protected static function organize(array $array): array
    {
        uksort($array, function ($a, $b) {
            $nBarrA = substr_count($a, '/');
            $nBarrB = substr_count($b, '/');

            if ($nBarrA != $nBarrB) return $nBarrB <=> $nBarrA;

            $arrayA = explode('/', $a);
            $arrayB = explode('/', $b);
            $na = '';
            $nb = '';
            $max = max(count($arrayA), count($arrayB));

            for ($i = 0; $i < $max; $i++) {
                $na .= match (true) {
                    (($arrayA[$i] ?? '@') == '@') => '1',
                    (($arrayA[$i] ?? '#') == '#') => '2',
                    (($arrayA[$i] ?? '') == '...') => '3',
                    default => '0'
                };
                $nb .= match (true) {
                    (($arrayB[$i] ?? '@') == '@') => '1',
                    (($arrayB[$i] ?? '#') == '#') => '2',
                    (($arrayB[$i] ?? '') == '...') => '3',
                    default => '0'
                };
            }

            $result = intval($na) <=> intval($nb);

            if ($result) return $result;

            $result = count($arrayA) <=> count($arrayB);

            if ($result) return $result * -1;

            $result = strlen($a) <=> strlen($b);

            if ($result) return $result * -1;
        });

        return $array;
    }

    /** Retorna o template que combina com a URL atual */
    protected static function getTemplateMatch(array $routes): ?string
    {
        $routes = self::organize($routes);

        $templates = array_keys($routes);

        foreach ($templates as $template)
            if (self::match($template))
                return $template;

        return null;
    }

    /** Verifica se um template combina com a URL atual */
    protected static function match(string $template): bool
    {
        $template = self::clearRoute($template);
        list($template) = self::explodeRoute($template);

        $uri = Request::path();

        $template = trim($template, '/');

        if (strpos($template, '?') !== false) {
            $paramsQuery = explode('?', $template);
            $template = array_shift($paramsQuery);
            foreach ($paramsQuery as $param)
                if (is_null(Request::query($param)))
                    return false;
        }

        $template = explode('/', $template);

        while (count($template)) {
            $esperado = array_shift($template);

            $recebido = array_shift($uri) ?? '';

            if ($recebido != $esperado) {

                if (is_blank($recebido))
                    return $esperado == '...';

                if ($esperado == '@') {
                    if (!is_numeric($recebido) || intval($recebido) != $recebido)
                        return false;
                } else if ($esperado != '#' && $esperado != '...') {
                    return false;
                }
            }

            if ($esperado == '...' && count($uri))
                $template[] = '...';
        }

        if (count($uri) != count($template))
            return false;

        return true;
    }

    /** Define os parametros da rota dentro do objeto de requisição */
    protected static function setParamnsData(?string $template, ?array $params): void
    {
        if (is_null($template))
            return;

        $uri = Request::path();
        $dataParams = [];

        foreach ($params ?? [] as $pos => $name) {
            $value = $uri[$pos];
            $dataParams[$name ?? count($dataParams)] = $value;
        }

        if (str_ends_with($template, '...')) {
            $template = explode('/', $template);
            array_pop($template);
            $dataParams = [...$dataParams, ...array_slice($uri, count($template))];
        }

        foreach ($dataParams as $var => $value)
            Request::set_route($var, $value);
    }
}
