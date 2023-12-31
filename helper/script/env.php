<?php

use Elegance\Core\Env;

Env::default('PORT', 8888);

Env::default('TERMINAL_URL', 'http://127.0.0.1:' . env('PORT'));

Env::default('CACHE', null);

Env::default('STM_200', 'ok');
Env::default('STM_201', 'created');
Env::default('STM_303', 'redirect');
Env::default('STM_400', 'bad request');
Env::default('STM_401', 'unauthorized');
Env::default('STM_403', 'forbidden');
Env::default('STM_404', 'not found');
Env::default('STM_405', 'method not allowed');
Env::default('STM_500', 'internal server error');
Env::default('STM_501', 'not implemented');
Env::default('STM_503', 'service unavailable');

Env::default('JWT', 'elegance-jwt-default-key');

Env::default('COOKIE_LIFE', '+1 days');
Env::default('COOKIE_DOMAIN', '');
