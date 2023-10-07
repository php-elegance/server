# Router

Controla rotas do sistema

    use Elegance/Router

## Criando rotas manualmente

A classe conta um metodo para adiconar rotas manualmente

**Router::add**: Adiciona uma rota para todas as requisições

    Router::add($template,$response);

> As ordem de declaração das rotas não importa pra a interpretação. A classe vai organizar as rotas da maneira mais segura possivel.

Para resolver as rotas, utilize o metodo **solve**

    Router::solve();

Para importar todos os arquivos do diretório **routes**, utilize o metodo **import**

    Router::import();

Se precisar importar apenas um arquivo, passe o nome do arquivo como parametro

    Router::import($fileName);

### Template

O template é a forma como a rota será encontrada na URL.

    Router::add('shop')// Reponde a URL /shop
    Router::add('blog')// Reponde a URL /blog
    Router::add('blog/post')// Reponde a URL /blog/post
    Router::add('')// Reponde a URL em branco

Para definir um parametro dinamico no template, utilize **[#]**

    Router::add('blog/[#]')// Reponde a URL /blog/[alguma coisa]
    Router::add('blog/post/[#]')// Reponde a URL /blog/post/[alguma coisa]

Caso a rota deva aceitar mais parametros alem do definido no template, utilize o sufixo **...**

    Router::add('blog...')// Reponde a URL /blog/[qualquer numero de parametros]

Para nomear os parametros dinamicos, pasta adicionar um nome ao **[#]**

    Router::add('blog/[#postId]')
    Router::add('blog/post/[#imageId]')

Você pode definir um valor que um parametro deva assumir.

    Router::add('blog/[#postSlut:nome-do-post]')
    Router::add('blog/[#postSlut:nome-de-outro-post]')

 > Os parametros dinamicos podem ser recuperados utilizando a classe [Request](https://github.com/php-elegance/server/blob/main/.doc/request.md)

    Request::route(); //Retorna todos os parametros
    Request::route(0); //Retorna o primeiro parametro
    Request::route('var'); //Retorna o parametro de nome var

**query**
Caso precise filtrar uma rota por um parametro existente no querystring, utilize o caracter **?**

    Router::add('blog?post','...');

É possivel adicionar mais de um parametro e filtro de query

    Router::add('blog?post?comment','...');

É possivel informar multiplos filtros usando a notação da querystring

    Router::add('blog?post&comment','...');

No caso dos filtros por query, a ordem do filtro não influencia. Rotas com os mesmo filtros serão subistituídas. 

    Router::add('blog?post?comment','...'); // Esta rota será subistituída
    Router::add('blog?comment?post','...'); // Esta rota será utilizada

### Respostas

A classe **Elegance/Action** é responsavel por tratar as respostas das rotas em forma de ações. veja [action](https://github.com/php-elegance/server/blob/main/.doc/action.md) para entender mais mais

A resposta da rota vai sempre ser a ação de resposta utilizando os parametros da URL como data

### Middlewares

Para adicionar midleware a uma rota, adicione o array de middlewaew como parametro adicionarl

    Router::add('route','response',[middlewares]);

Você pode definir middlewares para um grupo de rotas. Para isso, use o metodo **middleware**

    Router::middleware($middlewars,function(){
        Router::add('route1'...);
        Router::add('route2'...);
        Router::add('route3'...);
        Router::add('route4'...);
    });

Para definir uma middleware chamada globalmente, basta não informar uma funcion de routas

    Router::middleware([$middlewares]);
