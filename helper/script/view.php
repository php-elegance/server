<?php

namespace Elegance\Server;

use Elegance\Server\ViewRender\ViewRenderCss;
use Elegance\Server\ViewRender\ViewRenderJs;
use Elegance\Server\ViewRender\ViewRenderPhp;

View::supportedSet('html', '_content.html', ViewRenderPhp::class);
View::supportedSet('css', '_style.css', ViewRenderCss::class);
View::supportedSet('js', '_script.js', ViewRenderJs::class);

View::autoImportViewEx('css');
View::autoImportViewEx('js');

View::setPrepare('VIEW', fn ($ref, ...$params) => View::render($ref, [], ...$params));
View::setPrepare('URL', fn (...$params) => url(...$params));
