<?php

namespace Elegance;

Router::add([], [
    'favicon.ico' => fn () => Assets::send('#elegance-server/public/favicon.ico'),
    'assets...' => fn () => Assets::send('public/', ...Request::route()),
    '' => 'home'
]);
