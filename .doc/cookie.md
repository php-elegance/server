# Cookie

Controla cookies do php

A variavel de ambiente **COOKIE_TIME** determina o tempo de vida dos cookies em horas. Por padrão, utiliza-se o valor de **SESSION_TIME**

> Definir a variavel de ambiente **COOKIE_TIME** como **0**, faz com os cookies sejam excluidos quando o navegador for fechado

> Cookies com nome iniciado em **#** terão seus nomes codificados e seus valores cifrados

**check**
Verifica se um cookie existe ou se tem um valor igual ao fornecido

    Cookie::check(string $name): bool

**set**
Define um valor para um cookie

    Cookie::set(string $name, mixed $value = null): void

**get**
Retorna o valor de um cookie

    Cookie::get(string $name): mixed

**remove**
Remove um cookie

    Cookie::remove(string $name): void
