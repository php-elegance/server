<?php

namespace Elegance\Server;

use Elegance\Core\File;
use Elegance\Core\Import;
use Error;
use Exception;
use ReflectionMethod;

abstract class Router
{
    protected static array $prefix = [];

    protected static array $route = [];
    protected static array $globalMiddleware = [];

    protected static array $status = [];

    /** Adiciona rotas na lista de interpretação */
    static function add(array $middlewares, ?array $routes = null): void
    {
        if (func_num_args() == 1) {
            $routes = $middlewares;
            $middlewares = [];
        }

        foreach ($routes as $route => $response) {
            if (is_string($response) || is_httpStatus($response)) {
                list($template, $params) = self::explodeRoute($route);
                self::$route[$template] = [
                    $params,
                    $response,
                    $middlewares
                ];
            }
        }
    }

    /** Define middlewares globais para as as rotas */
    static function globalMiddleware(array $middlewares)
    {
        self::$globalMiddleware = [...self::$globalMiddleware, ...$middlewares];
    }

    /** Define um prefixo de ação para uma resposta de rota */
    static function prefix(string $prefix, string $response)
    {
        self::$prefix[$prefix] = $response;
    }

    /** Adiciona um controlador para tratar status de resposta em Errro ou Exception */
    static function status(int|string $status, ?string $controller = null)
    {
        if (!is_httpStatus($status)) {
            $controller = $status;
            $status = 0;
        }

        self::$status[$status] = $controller;
    }

    /** Resolve a requisição atual */
    static function solve()
    {
        try {
            self::loadShemeRoutes();

            $template = self::getTemplateMatch(self::$route);

            $route = !is_null($template) ? self::$route[$template] : [null, STS_NOT_FOUND, []];

            list($params, $response, $middleware) = $route;

            self::setParamnsData($template, $params);

            $response = self::executeResponse($response, Request::data(), $middleware);
        } catch (Error | Exception $e) {
            $response = self::executeStatus($e);
        }

        Response::content($response);
        Response::send();
    }

    /** Executa um erro */
    protected static function executeStatus(Exception|Error $e)
    {
        if (env('DEV')) {
            Response::header('Elegance-Error-Message', $e->getMessage());
            Response::header('Elegance-Error-Code', $e->getCode());
            Response::header('Elegance-Error-File', $e->getFile());
            Response::header('Elegance-Error-Line', $e->getLine());
        }

        $code = $e->getCode();

        $controller = self::$status[$code] ?? self::$status[0] ?? false;

        if (!$controller)
            throw $e;

        $action = self::getReponseAction($controller);

        return $action($controller, ['e' => $e]);
    }

    /** Executa a resposta da rota retornando a resposta final */
    protected static function executeResponse(string|int $response, array $params, array $middleware)
    {
        foreach (self::$prefix as $prefix => $responsePrefix) {
            if (str_starts_with($response, $prefix)) {
                $response = substr($response, strlen($prefix));
                $params = ['response' => $response];
                $response = $responsePrefix;
                $middleware = [];
            }
        }

        $action = self::getReponseAction($response);

        return Middleware::run([...self::$globalMiddleware, ...$middleware], fn () => $action($response, $params));
    }

    protected static function getReponseAction($response)
    {
        if (is_httpStatus($response))
            return fn ($response) => throw new Exception('', $response);

        return function ($response, $data) {
            try {
                list($controller, $method) = explode(':', $response ?? '');

                if (!str_starts_with($controller, '='))
                    $controller = "=controller.$controller";

                $controller = explode('.', substr($controller, 1));
                $controller = array_map(fn ($v) => ucfirst($v), $controller);
                $controller = "\\" . implode("\\", $controller);

                if (method_exists($controller, '__construct')) {
                    $reflection = new ReflectionMethod($controller, '__construct');
                    $reflectionParams = [];
                    foreach ($reflection->getParameters() as $param) {
                        $name = $param->getName();
                        if (isset($data[$name])) {
                            $reflectionParams[] = $data[$name];
                        } else if ($param->isDefaultValueAvailable()) {
                            $reflectionParams[] = $param->getDefaultValue();
                        } else {
                            throw new Exception("Parameter [$name] is required", STS_INTERNAL_SERVER_ERROR);
                        }
                    }
                    $controller = new $controller(...$reflectionParams);
                } else {
                    $controller = new $controller();
                }

                $reflection = new ReflectionMethod($controller, $method);
                $reflectionParams = [];
                foreach ($reflection->getParameters() as $param) {
                    $name = $param->getName();
                    if (isset($data[$name])) {
                        $reflectionParams[] = $data[$name];
                    } else if ($param->isDefaultValueAvailable()) {
                        $reflectionParams[] = $param->getDefaultValue();
                    } else {
                        throw new Exception("Parameter [$name] is required", STS_INTERNAL_SERVER_ERROR);
                    }
                }

                return $controller->{$method}(...$reflectionParams);
            } catch (Exception | Error $e) {
                throw $e;
            }
        };
    }

    /** Carrega o esquema de rotas */
    protected static function loadShemeRoutes()
    {
        if (!env('DEV') && File::check('routes.json')) {
            $scheme = jsonFile('routes');
            self::$prefix = $scheme['prefix'];
            self::$globalMiddleware = $scheme['globalMiddleware'];
            self::$status = $scheme['status'];
            self::$route = $scheme['route'];
        } else {
            Import::only("routes");
            self::$route = self::organize(self::$route);
            $scheme = [
                'prefix' => self::$prefix,
                'globalMiddleware' => self::$globalMiddleware,
                'status' => self::$status,
                'route' => self::$route,
            ];
            $scheme = jsonFile('routes', $scheme);
        }
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
