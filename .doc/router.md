# Router

Controla rotas do sistema

    use Elegance/Router

## Criando rotas manualmente

A classe conta um metodo para adiconar rotas manualmente

**Router::add**: Adiciona linhas de rotas a lista de interpretação

    Router::add( [...middlewares], [...routes]);

> As ordem de declaração das linhas rotas não importa pra a interpretação. A classe vai organizar as rotas da maneira mais segura possivel.

Para resolver as rotas, utilize o metodo **solve**

    Router::solve();

 > O metodo **solve** vai automáticamente importar o arquivo **routes.php** na raís do seu projeto.

### Template

O template é a forma como a rota será encontrada na URL.

    'shop' // Reponde a URL /shop
    'blog' // Reponde a URL /blog
    'blog/post' // Reponde a URL /blog/post

Para definir um parametro dinamico no template, utilize **[#]**

    'blog/[#]' // Reponde a URL /blog/[alguma coisa]
    'blog/post/[#]' // Reponde a URL /blog/post/[alguma coisa]

Caso a rota deva aceitar mais parametros alem do definido no template, utilize o sufixo **...**

    'blog...' // Reponde a URL /blog/[qualquer numero de parametros]

Para nomear os parametros dinamicos, pasta adicionar um nome ao **[#]**

    'blog/[#postId]'
    'blog/post/[#imageId]'

> Você pode ocultar o **#** declarando **[#name]** apenas como **[name]**

Você pode definir um valor que um parametro deva assumir.

    'blog/[#postSlut:nome-do-post]'
    'blog/[#postSlut:nome-de-outro-post]'

 > Os parametros dinamicos podem ser recuperados utilizando a classe [Request](https://github.com/php-elegance/server/blob/main/.doc/request.md)

    Request::route(); //Retorna todos os parametros
    Request::route(0); //Retorna o primeiro parametro
    Request::route('var'); //Retorna o parametro de nome var

**query**
Caso precise filtrar uma rota por um parametro existente no querystring, utilize o caracter **?**

    'blog?post'

É possivel adicionar mais de um parametro e filtro de query

    'blog?post?comment'

É possivel informar multiplos filtros usando a notação da querystring

    'blog?post&comment'

No caso dos filtros por query, a ordem do filtro não influencia. Rotas com os mesmo filtros serão subistituídas. 

    'blog?post?comment' // Esta rota será subistituída
    'blog?comment?post' // Esta rota será utilizada

### Respostas

A classe **Elegance/Action** é responsavel por tratar as respostas das rotas em forma de ações. veja [action](https://github.com/php-elegance/server/blob/main/.doc/action.md) para entender mais mais

A resposta da rota vai sempre ser a ação de resposta utilizando os parametros da URL como data

### Exemplo de criação de rotas

    Router::add(['middleware1', 'middleware2'], [
        '' => 'home',
        'blog' => 'blog',
        'blog/[post]' => 'blog.post'
    ]);

### Esquema de rotas

Se em algum momento da aplicação precisar obter o esquema de rotas, utilize o metodo **getScheme**

    Router::getScheme();

O retorno é o equema de rotas em forma de array ['template', 'params', 'middleware']

**Exemplo de um esquema de rotas**

    [
        [
            "template"=>'favicon.ico',
            "params"=>null,
            "middleware"=>[
                "elegance.cros",
                "elegance.json"
            ]
        ],
        [
            "template"=>'assets/...',
            "params"=>null,
            "middleware"=>[
                "elegance.cros",
                "elegance.json"
            ]
        ],
    ]