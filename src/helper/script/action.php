<?php

namespace Elegance;

use Exception;

Action::prefix('#', fn ($response) => prepare($response, Request::route()));

Action::prefix('>', fn ($response) => redirect($response));

Action::prefix('>>', fn ($response) => redirect($response, ...Request::route()));

Action::prefix('', function ($__ACTION__, array $__DATA = []) {
    $__ACTION__ = str_replace('.', '/', $__ACTION__);
    $__ACTION__ = File::setEx($__ACTION__, 'php');
    $__ACTION__ = path('app', $__ACTION__);

    foreach (array_keys($__DATA) as $__KEY__)
        if (!is_numeric($__KEY__))
            $$__KEY__ = $__DATA[$__KEY__];

    if (!File::check($__ACTION__))
        throw new Exception('File action not found', STS_INTERNAL_SERVER_ERROR);

    ob_start();
    $__RESPONSE__ = include $__ACTION__;
    $__OUTPUT__ = ob_get_clean();

    if (!empty($__OUTPUT__))
        $__RESPONSE__ = $__OUTPUT__;

    if (!$__RESPONSE__ == 1)
        $__RESPONSE__ = STS_OK;

    if (!is_stringable($__RESPONSE__))
        return Action::run($__RESPONSE__, $__DATA);

    return View::renderString($__RESPONSE__, 'php', $__DATA);
});
