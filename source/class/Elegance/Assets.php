<?php

namespace Elegance;

use Exception;

abstract class Assets
{
    /** Envia um arquivo ou view assets como resposta da requisição */
    static function send(): never
    {
        self::loadResponse(...func_get_args());
        Response::send();
    }

    /** Realiza o download de um arquivo ou view assets como resposta da requisição */
    static function download(): never
    {
        self::loadResponse(...func_get_args());
        Response::download(true);
        Response::send();
    }

    /** Carrega um arquivo ou view na resposta da aplicação */
    static function load(): void
    {
        self::loadResponse(...func_get_args());
    }

    /** Retorna o ResponseFile do arquivo */
    protected static function loadResponse(): void
    {
        $path = path(...func_get_args());

        if (!File::check($path))
            throw new Exception("Arquivo não encontrado", STS_NOT_FOUND);

        if (View::suportedCheck(File::getEx($path))) {
            Response::content(View::renderFile("=$path"));
        } else {
            Response::content(Import::content($path));
        }

        Response::type(File::getEx($path));
        Response::download(File::getOnly($path));
        Response::download(false);
        Response::status(STS_OK);
    }
}