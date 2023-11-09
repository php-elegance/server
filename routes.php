<?php

use Elegance\Server\Router;

Router::prefix('#', '=elegance.server.controller.prefix:show');
Router::prefix('>', '=elegance.server.controller.prefix:redirect');
Router::prefix('>>', '=elegance.server.controller.prefix:redirectWithRequestParameters');

Router::globalMiddleware(['elegance.cros', 'elegance.json']);

Router::add([
    'favicon.ico' => '=elegance.server.controller.assets:favicon',
    'assets...' => '=elegance.server.controller.assets:auto',
    '' => '#Bem vindo ao ELEGANCE',
    'teste' => '>',
]);
