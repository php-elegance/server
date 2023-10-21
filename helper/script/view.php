<?php

namespace Elegance;

View::suportedSet('html', '_content.html');
View::suportedSet('css', '_style.css');
View::suportedSet('scss', '_style.scss');
View::suportedSet('js', '_script.js');

View::autoImportViewEx('css');
View::autoImportViewEx('scss');
View::autoImportViewEx('js');

View::setPrepare('view', fn ($ref, ...$params) => View::renderFile($ref, [], ...$params));
View::setPrepare('url', fn (...$params) => url(...$params));
