<?php

namespace Controller;

use Exception;

class Error
{
    /** Redirecionamento */
    function redirect(Error|Exception $e)
    {
        return 'Redirecionamento';
    }

    /** Sintaxe intorreta */
    function bad_request(Error|Exception $e)
    {
        return 'Sintaxe intorreta';
    }

    /** Requer permissão */
    function unauthorized(Error|Exception $e)
    {
        return 'Requer permissão';
    }

    /** Proibido */
    function forbidden(Error|Exception $e)
    {
        return 'Proibido';
    }

    /** Não encontrado */
    function not_found(Error|Exception $e)
    {
        return 'Não encontrado';
    }

    /** Método não permitido */
    function method_not_allowed(Error|Exception $e)
    {
        return 'Método não permitido';
    }

    /** Erro interno do servidor */
    function internal_server_error()
    {
        return 'Erro interno';
    }

    /** Não implementado */
    function not_implemented(Error|Exception $e)
    {
        return 'Não implementado';
    }

    /** Indisponível */
    function service_unavailable(Error|Exception $e)
    {
        return 'Indisponível';
    }
}
