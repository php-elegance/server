<?php

namespace Elegance\Server\Controller;

use Elegance\Server\Instance\Input;

class Teste
{
    function teste()
    {
        $input = new Input();
        $input->field('nome');

        $input->dataRecived();
    }
}
