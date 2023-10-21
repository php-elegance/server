<?php

namespace Elegance;

// php mx install.structure

return function () {
    Dir::create("action");
    Dir::create("assets");
    Dir::create("class");
    Dir::create("helper");
    Dir::create("helper/constant");
    Dir::create("helper/function");
    Dir::create("helper/script");
    Dir::create("routes");
    Dir::create("source");
    Dir::create("view");
    Terminal::echo('Estrutura de pastas instalada');
};
