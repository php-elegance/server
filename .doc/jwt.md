# JWT
Cria e valída tokens jwt

    use \Elegance\Jwt;

### Utilizando a classe estatica

> A classe estatica sempre usa o pass definido nas variaveis de ambiente

    use Elegance\Jwt;

Retorna um token JWT com o conteúdo

    Jwt::on(mixed $payload, ?string $key = null): string

Retorna o token conteúdo de um token JWT

    Jwt::off(mixed $token, ?string $key = null): mixed

Verifica se uma variavel é um token JWT válido

    Jwt::check(mixed $var, ?string $key = null): bool
