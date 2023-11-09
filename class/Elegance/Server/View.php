<?php

namespace Elegance\Server\Server;

use Elegance\ViewRender\ViewRender;

abstract class View
{
    protected static array $current = [];

    protected static array $prefix = ['=' => ''];

    protected static array $prepare = [];

    protected static array $suported = ['php' => 'php'];

    protected static array $autoImportViewEx = [];

    protected static array $autoImportViewFile = ['php' => '_content.php'];

    /** Renderiza um arquivo como uma view */
    static function renderFile(string $viewRef, array $data = [], ...$params): string
    {
        $file = self::getViewFile($viewRef);

        if (!$file) return '';

        self::currentOpen($file, $data);

        if (self::currentGet('type') == 'php') {
            list($data, $content) = (function ($__FILEPATH__, $__DATA) {
                foreach (array_keys($__DATA) as $__KEY__)
                    if (!is_numeric($__KEY__))
                        $$__KEY__ = $__DATA[$__KEY__];

                ob_start();
                $__RETURN__ = require $__FILEPATH__;
                $__OUTPUT__ = ob_get_clean();

                if (is_stringable($__RETURN__))
                    $__OUTPUT__ = $__RETURN__;

                return [$__DATA, $__OUTPUT__];
            })(self::currentGet('ref'), self::currentGet('data'));

            self::currentSet('data', is_array($data) ? $data : [$data]);

            if (File::getOnly(self::currentGet('ref')) == '_content.php') {
                foreach (self::$autoImportViewEx as $ex) {
                    $imporFile = self::$autoImportViewFile[$ex];
                    $content .= "[#view:.$imporFile]";
                }
            }
        } else {
            $content = Import::content(self::currentGet('ref'));
        }

        $content = self::renderize($content, $params);

        self::currentClose();

        return $content;
    }

    /** Renderiza uma string como uma view */
    static function renderString(string $viewString, string $type, array $data = [])
    {
        if (!self::suportedCheck($type))
            return '';

        self::currentOpen(null, $data);
        self::currentSet('type', $type);
        $content = self::renderize($viewString);
        self::currentClose();

        return $content;
    }

    /** Define diretórios para referencia de view com determinados */
    static function prefix(string $prefix, string $path)
    {
        self::$prefix[$prefix] = $path;
        uksort(self::$prefix, fn ($a, $b) => strlen($b) <=> strlen($a));
    }

    /** Adiciona extensões que devem ser importadas automáticamente em uma view php */
    static function autoImportViewEx(string $ex)
    {
        self::$autoImportViewEx[] = $ex;
    }

    /** Aplica as regras de renderização da view atual */
    protected static function renderize(string $content, array $params = [])
    {
        $__onescope = md5(uniqid());
        $__scope = self::currentGet('ref') ? md5(Dir::getOnly(self::currentGet('ref'))) : $__onescope;

        $content = str_replace(
            ['__onescope', '__scope'],
            ["_$__onescope", "_$__scope"],
            $content
        );

        $render = '\\Elegance\\ViewRender\\ViewRender' . ucfirst(self::$suported[self::currentGet('type')]);

        if (!class_exists($render) || !is_extend($render, ViewRender::class))
            $render = ViewRender::class;

        $content = $render::renderizeAction($content, $params);

        return $content;
    }

    /** Resolve uma referencia em um nome de arquivo  */
    protected static function getViewFile($viewRef): string|bool
    {
        $basePath = null;

        foreach (self::$prefix as $prefix => $path)
            if (is_null($basePath) && str_starts_with($viewRef, $prefix)) {
                $basePath = $path;
                $viewRef = substr($viewRef, strlen($prefix));
            }

        if (str_starts_with($viewRef, '.')) {
            $viewRef = substr($viewRef, 1);
            if (count(self::$current))
                $basePath = Dir::getOnly(self::currentGet('ref') ?? '');
        }

        $basePath = $basePath ?? 'view';

        $viewRef = explode('.', $viewRef);

        if (count($viewRef) > 1) {
            $viewEx = array_pop($viewRef);
            if (!self::suportedCheck($viewEx)) {
                $viewRef[] = $viewEx;
                $viewEx = null;
            }
        }

        $viewRef = implode('/', $viewRef);

        $viewEx = $viewEx ?? 'php';

        $viewRef = path($basePath, $viewRef);
        $viewRef = trim($viewRef, '/');

        $file = "$viewRef.$viewEx";

        if (!File::check($file)) {
            $file = self::$autoImportViewFile[$viewEx];
            $file = "$viewRef/$file";
            if (!File::check($file))
                return false;
        }

        return $file;
    }

    /** Inicializa uma view marcando como atual */
    protected static function currentOpen(?string $viewRef, array $data): bool
    {
        $key = Code::on($viewRef ?? uniqid());

        if (isset(self::$current[$key]))
            return false;

        $viewEx = is_null($viewRef) ? 'php' : File::getEx($viewRef);

        $data = [
            ...self::currentGet('data') ?? [],
            ...$data
        ];

        self::$current[$key] = [
            'key' => $key,
            'ref' => $viewRef,
            'type' => $viewEx,
            'data' => $data
        ];

        return true;
    }

    /** Finaliza a view atual */
    protected static function currentClose(): void
    {
        array_pop(self::$current);
    }

    /** Retorna uma variavel da view atual */
    protected static function currentGet($var)
    {
        if (count(self::$current)) {
            $current = self::$current;
            $current = array_pop($current);
            return $current[$var] ?? null;
        }
        return null;
    }

    /** Define uma variavel da view atual */
    protected static function currentSet($var, $value)
    {
        $key = self::currentGet('key');
        if ($key) self::$current[$key][$var] = $value;
    }

    /** Verifica se a view pai é de um dos tipos fornecidos  */
    protected static function parentType(...$types): bool
    {
        if (count(self::$current) <= 1)
            return false;

        $parentKey = array_keys(self::$current);
        $parentKey = $parentKey[count($parentKey) - 2];

        $parentType = self::$current[$parentKey]['type'];
        $parentType = self::$suported[$parentType];

        foreach ($types as $type)
            if (strtolower($type) == $parentType)
                return true;

        return false;
    }

    /** Verifica se o tipo de view pode ser renderizado */
    static function suportedCheck($type)
    {
        return isset(self::$suported[strtolower($type)]);
    }

    /** Adiciona suporte a um tipo de arquivo */
    static function suportedSet(string $ex, ?string $autoImportFile = null, ?string $renderType = null)
    {
        $ex = strtolower($ex);
        $renderType = strtolower($renderType ?? $ex);

        if (!self::suportedCheck($ex))
            self::$suported[$ex] = $renderType;

        if ($autoImportFile)
            self::$autoImportViewFile[$ex] = $autoImportFile;
    }

    /** Adiciona uma tag ao prepare global das views */
    static function setPrepare($tag, $response)
    {
        self::$prepare[$tag] = $response;
    }

    /** Aplica os prepare de view em uma string */
    protected static function applyPrepare($string)
    {
        $string = prepare($string, [...self::currentGet('data'), ...self::$prepare]);
        return $string;
    }
}