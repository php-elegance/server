# Middleware
Gerencia fila de middlewares para projetos Elegance

    use \Elegance\Middleware;

---
### Estrutura

As middlewares são funções que recebem um valor, realizam uma ação e chamam a proxima. 
O template basico de uma middleware é o seguinte

    function (Closure $next){
        return $next();
    }

Caso a middlewares seja uma classe, deve ser implementado o metodo **__invoke**

    function __invoke(Closure $next){
        return $next();
    }

### Criando middlewares

    php mx create.middleware [nomeDaMiddleware]

Isso vai criair uma classe de middleware dentro do diretório **source/Middleware** com o nome fornecido

### Executando middlewares
Para executar middlewares, utilize o metodo estatico **run**

    Middleware::run(array $queue, $action)