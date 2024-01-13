# View

Camada de visualização para aplicações Mx

### Diretório

O diretório **view** contem os arquivos para montar a camada de visualiação.

### Utilização

Ao chamar uma view é o mesmo que executar um [prepare](https://github.com/php-elegance/core/blob/main/.doc/prepare.md) em seus arquivos. A classe se encarrega de montar a view da melhor forma possivel. Podem ser chamados via metodo, helper.

    View::render($ref,$prepare):string;
    view($ref,$prepare):string;

Você pode chamar views dentro de um subdiretório, para isso, adicione o caminho relativo para o arquivo partindo do diretório view/page/

    view('index.html') // Chama o arquivo view/index.html
    view('nav.top.html') // Chama o arquivo view/nav/top.html

Caso nenhuma extensão seja fornecida, a classe vai tentar importar um arquivo php

    view('index.html') // Chama a view index.html
    view('index') // Chama a view index.php

### Escrevendo view

Uma view pode ser um arquivo HTML, JS, CSS PHP e VUE. Organize os arquivos em subdiretórios da forma que for mais confortável. Escreva o arquivo de view normalmente

    <h1>Isso é uma view</h1>

Ao chamar uma view você pode fornecer um array de prepare. Todas a variaveis do array vão estar disponiveis no arquivo de view

    //view/page/index.html
    <h1>[#name]</h1>

    //Chamada da view
    view('index.html',['name'=>'Pedro']);

    //Saída
    <h1>Pedro</h1>

Você pode utilizar a tag prepare **[#view]** chamar views encadeadas.
Usar a tag prepare junto com o prefixo **.** (ponto) vai importar um aquivo de view utilizando o prepare da chamada original e partindo do diretório Atual da view.

Se precisar chamar um arquivo de view, que não esteja no diretório **view**, utilize o prefixo **=**


    [#VIEW:page.html] // Carrega arquivo view/page.html
    [#VIEW:.page.html] // Carrega arquivo [currentPath]/page.html
    [#VIEW:=libray/page.html] // Carrega arquivo libray/page.html

Para adicionar prefixos extras, utilize o metodo **prefix**

    View::prefix(string $prefix, string $path);

Mesmo que o prepare seja executado antes do arquivo, alguns editores podem reconhecer a tag prepare como um erro.
Para evitar a sinalizalção de erro do editor pode-se colocar a tag prepare atras de um comentário.

    [#VIEW:...]
    <!-- [#VIEW:...] -->
    <!--[#VIEW:...]-->
    //[#VIEW:...]
    /* [#VIEW:...] */
    /*[#VIEW:...]*/

Pode-se misturar tipos de arquivo com as chamadas via prepare. Lembre-se de encapsular corretamente o conteúdo dos aquivos.

    //view/page/style.css
    h1{ color: red; }

    //view/page/index.html
    <h1>Ola mundo</h1>
    <style>[#VIEW:style.css]</style>

    //Saída
    <h1>Ola mundo</h1>
    <style>h1{ color: red; }</style>

### Tipos de view

Em geral, todas as views tem o mesmo comportamento. Todas tem seu conteúdo importado e recebem o tratamento via prepare.

Os tipos de view suportados são

- ***.php**: Arquivo com lógica e estrutura HTML
- ***.html**: Arquivo com estrutura HTML
- ***.css**: Arquvio de estilização
- ***.js**: Arquvio de scripts para o frontend

Você pode adicionar mais tipos de arquivo utilizando o metodo estatico **supportedSet**

    View::supportedSet('scss','_style_.scss');

Se preferir que uma view responda como um tipo diferente, adicione um terceiro parametro ao metodo **supportedSet**

    View::supportedSet('scss','_style_.scss','css'); // Views SCSS serão tratadas como view CSS


### View dinamica PHP

Exclusivamente em arquivos PHP, você pode utilizar a variavel **$__DATA** para alterar as variaveis da view ativa.

    $__DATA['color'] = 'red'; // Altera a propriedade de prepare color da view ativa

Assim é possivel criar views diamicas css e js em arquivos php

- As tags **script** minificadas e combinadas
- As tags **style** minificadas, compiladas e combinadas
- Todo script será movidos para baixo do conteúdo
- Toda estilização a será movida para cima do conteúdo
- Em views inpath, subviews **.css**, **.js** serão importadas automáticamente

Para adicionar mais imports automáticos, utilize o metodo **autoImportViewEx**

    View::autoImportViewEx('css');

### View dinamica HTML

- As tags **script** minificadas e combinadas
- As tags **style** minificadas e combinadas
- Todo script será movidos para baixo do conteúdo
- Toda estilização a será movida para cima do conteúdo

### Considerações view CSS

As views CSS terão sem conteúdo minificado antes do retono. Como seu conteúdo é importado via PHP, a chamada **include** do css não deve ser utilizada. Ao invez disso, utilize a chamada de views para obter o mesmo resultado.

    import './newFile.css'// Vai gerar um Erro 500
    [#VIEW:newFile.css]// Obtem o resultado do import
    import url(...)// Pode ser usado normalmente

> A classe View vai ignorar estilos ideinticos a outros estilos já inseridos na requisição. Este comportameto acontece apenas em importações de arquivos, não afeta a estilização via tag style

### Considerações view JS

As views JS terão sem conteúdo minificado antes do retono.

> A classe View vai ignorar scripts ideinticos a outros scripts já inseridos na requisição. Este comportameto acontece apenas em importações de arquivos, não afeta a estilização via tag scripts
