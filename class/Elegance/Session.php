<?php

namespace Elegance;

abstract class Session
{
    /** Retorna o valor de uma variavel de sessão */
    static function get(string $name): ?string
    {
        return $_SESSION[$name] ?? null;
    }

    /** Define um valor para uma variavel de sessão */
    static function set(string $name, ?string $value): void
    {
        $_SESSION[$name] = $value;
    }

    /** Verifica se uma variavel de sessão existe ou se tem um valor igual ao fornecido */
    static function check(string $name): bool
    {
        return !is_null(self::get($name));
    }

    /** Remove uma variavel de sessão */
    static function remove(string $name): void
    {
        static::set($name, null);
    }
}
