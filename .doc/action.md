# Action

Chamadas de ação

    use \Elegance\Action;

**run**:  Retorna o resultado de uma chamada de ação

    Action::run(String|Int|Closure $call, array $data = [])

**get**:  Retorna uma função de execução de uma chamade de ação

    Action::get($response, array $data = []): Closure

## Ações registradas

### ACTION
É a resposta padrão para qualquer action string sem perfixo
Para fazer com que um arquivo do diretório **action** responda pela ação, informe o caminho do arquivo como resposta.

    Action::run('home'); // Retorno arquivo action/home.php

Caminhos podem ser separados por **.** ou **/**

    Action::run('blog/post'); // Retorno arquivo action/blog/post.php
    Action::run('blog.post'); // Retorno arquivo action/blog/post.php

O arquivo pode deve retornar um objeto Closure, um array json, um stauts HTTP ou um conteúdo view.

**Exemplo de retorno de conteúdo view**
    
    <?php 
    echo 'ola mundo';

**Exemplo de retorno de status**

    <?php 
    return STS_404;

**Exemplo de retorno de função anonima**

    <?php 
    return function(){...};

**Exemplo de retorno de classe anonima**

    <?php 
    return new class{
        function __invoke(){
            ...
        }
    };

**Exemplo de retorno de HTML puro**

    <h1>Ola mundo</h1>

### callable
Executando uma ação com uma função anonima a respota será o retorno da função

    Action::run('', function (){
        return ...
    });

### status
Pode adicionar um status HTTP para servir de resposta para a ação

    Action::run(STS_NOT_FOUND);

### dbug
Pode adicionar rápidamente uma resposta de texto para responder pela ação utilizando o prefixo **#**

    Action::run('#ola mundo')

### redirec
Use o prefixo **>** para redirecionar para uma URL

    Action::run('>outrapaginas')

Use o prefixo **>>** para redirecionar para uma URL mantendo os paraemtros atuais

    Action::run('>>error')


## prefixos extras
Para registar um prefixo extra para ações, utilize o metodo **prefix**

    Action::prefix(string $prefix, Closure $action);
