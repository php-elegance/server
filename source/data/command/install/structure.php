<?php

namespace Elegance;

// php mx install.structure

return function () {
    Dir::create("app");
    Dir::create("library");
    Dir::create("library/assets");
    Dir::create("routes");
    Dir::create("source");
    Dir::create("source/class");
    Dir::create("source/helper");
    Dir::create("source/helper/constant");
    Dir::create("source/helper/function");
    Dir::create("source/helper/script");
    Dir::create("view");
    Terminal::echo('Estrutura de pastas instalada');
};
