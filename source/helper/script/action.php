<?php

namespace Elegance;

Action::prefix('#', fn ($response) => prepare($response, Request::route()));

Action::prefix('>', fn ($response) => redirect($response));

Action::prefix('>>', fn ($response) => redirect($response, ...Request::route()));

Action::prefix('', function ($__APP__, array $__DATA = []) {
    $__APP__ = str_replace('.', '/', $__APP__);
    $__APP__ = File::setEx($__APP__, 'php');
    $__APP__ = path('app', $__APP__);

    foreach (array_keys($__DATA) as $__KEY__)
        if (!is_numeric($__KEY__))
            $$__KEY__ = $__DATA[$__KEY__];

    ob_start();
    $__RESPONSE__ = include $__APP__;
    $__OUTPUT__ = ob_get_clean();

    if (!empty($__OUTPUT__))
        $__RESPONSE__ = $__OUTPUT__;

    if (!$__RESPONSE__ == 1)
        $__RESPONSE__ = STS_OK;

    if (!is_string($__RESPONSE__))
        return Action::run($__RESPONSE__, $__DATA);

    return View::renderString($__OUTPUT__, 'php', $__DATA);
});
