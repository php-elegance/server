<?php

namespace Elegance\ViewRender;

abstract class ViewRenderCss extends ViewRender
{
    protected static array $importedHash = [];

    protected static array $media = [];

    protected static array $prepareReplace = [
        '/* [#' => '[#',
        '] */' => ']',
        '// [#' => '[#'
    ];

    /** Aplica ações extras ao renderizar uma view */
    protected static function renderizeAction(string $content, array $params = []): string
    {
        $encaps = $params[0] ?? true;

        $content = str_replace(array_keys(self::$prepareReplace), array_values(self::$prepareReplace), $content);
        $content = self::applyPrepare($content);

        $hash = md5($content);

        if (!IS_TERMINAL && isset(self::$importedHash[$hash]))
            return '';

        self::$importedHash[$hash] = true;

        if (!self::parentType('css')) {
            if (count(self::$current) == 1) {
                $content = self::minify($content);
            } elseif ($encaps) {
                $content = "<style>\n$content\n</style>";
            }
        }

        return $content;
    }

    /** Adiciona um tratamento para @media especial */
    static function media($name, $value)
    {
        self::$media[$name] = $value;
    }

    /** Minifica uma string css */
    static function minify(string $style): string
    {
        foreach (self::$media as $media => $value)
            $style = str_replace("@media $media", "@media $value", $style);

        return $style;
    }
}
