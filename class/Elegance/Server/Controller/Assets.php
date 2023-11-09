<?php

namespace Controller;

use Elegance\File;
use Elegance\Request;

class Assets
{
    function favicon(): void
    {
        $path = 'library/assets/favicon.ico';

        if (!File::check($path))
            $path = '#elegance-server/library/assets/favicon.ico';

        $this->send($path);
    }

    function auto(): void
    {
        $path = path('library/assets/', ...Request::route());
        $this->send($path);
    }

    protected function send($path): void
    {
        Assets::send($path);
    }
}
