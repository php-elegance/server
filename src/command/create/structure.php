<?php

namespace Elegance;

// php mx create.structure

return function () {
    Dir::create("class");
    Dir::create("front");
    Dir::create("front/view");
    Dir::create("library");
    Dir::create("library/assets");
    Dir::create("src");
    Dir::create("src/helper");
    Dir::create("src/helper/constant");
    Dir::create("src/helper/function");
    Dir::create("src/helper/script");
    Terminal::echo('Estrutura de pastas instalada');
};
