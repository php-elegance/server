<?php

namespace Elegance;

// php mx create.structure

return function () {
    Dir::create("app");
    Dir::create("public");
    Dir::create("class");
    Dir::create("source");
    Dir::create("source/helper");
    Dir::create("source/helper/constant");
    Dir::create("source/helper/function");
    Dir::create("source/helper/script");
    Dir::create("source/routes");
    Dir::create("view");
    Terminal::echo('Estrutura de pastas instalada');
};
