<?php

// middleware elegance.session

use Elegance\Code;
use Elegance\Cookie;
use Elegance\Session;

return function ($next) {
    $timeout = intval(env('SESSION_TIME'));
    $timeout *= 60 * 60;

    session_set_cookie_params($timeout, env('SESSION_PATH'), env('SESSION_DOMAIN'), true, true);

    session_start();

    if (!Session::check('SESSION_ID') || !Code::check(session_id()) || session_id() != Session::get('SESSION_ID')) {
        session_destroy();
        $key = Code::on(uniqid());
        session_id($key);
        session_start();
        Session::set('SESSION_ID', $key);
    }

    Cookie::set(session_name(), session_id());

    return $next();
};
