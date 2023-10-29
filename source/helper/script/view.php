<?php

namespace Elegance;

View::suportedSet('html', '_content.html', 'php');
View::suportedSet('css', '_style.css');
View::suportedSet('js', '_script.js');

View::autoImportViewEx('css');
View::autoImportViewEx('js');

View::setPrepare('view', fn ($ref, ...$params) => View::renderFile($ref, [], ...$params));
View::setPrepare('url', fn (...$params) => url(...$params));
