<?php

namespace Elegance\Server\ViewRender;

use Elegance\Server\View;

abstract class ViewRender extends View
{
    /** Aplica ações extras ao renderizar uma view */
    protected static function renderizeAction(string $content, array $params = []): string
    {
        return self::applyPrepare($content);
    }
}
