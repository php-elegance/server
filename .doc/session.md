# Session

Controla sessão do php

A variavel de ambiente **SESSION_TIME** determina o tempo de vida da sessão em horas. O padrão é 24

> Variaveis de sessão com nome iniciado em **#** serão tratadas como **FLASH** (podem ser recuperadas apenas uma vez)

**check**
Verifica se uma variavel de sessão existe ou se tem um valor igual ao fornecido

    Session::check(string $name): bool

**set**
Define um valor para uma variavel de sessão

    Session::set(string $name, mixed $value = null): void

**get**
Retorna o valor de uma variavel de sessão

    Session::get(string $name): mixed

**remove**
Remove uma variavel de sessão

    Session::remove(string $name): void
