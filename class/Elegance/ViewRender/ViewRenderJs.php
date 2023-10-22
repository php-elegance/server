<?php

namespace Elegance\ViewRender;

abstract class ViewRenderJs extends ViewRender
{
    protected static array $importedHash = [];

    protected static array $prepareReplace = [
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

        if (!self::parentType('js')) {
            if (count(self::$current) == 1) {
                $content = self::minify($content);
            } elseif ($encaps) {
                $content = "<script>\n$content\n</script>";
            }
        }

        return $content;
    }

    /** Minifica uma string javascript */
    static function minify(string $script): string
    {
        return preg_replace(array("/\s+\n/", "/\n\s+/"), array("\n", "\n"), $script);
    }
}
