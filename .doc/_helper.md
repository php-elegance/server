# Helpers

## Command

**create.structure**: Cria a estrutua de pastas para do projeto

    php mx create.structure

**install.index**: Instala o arquivo index.php padrão

    php mx install.index

**middleware**: Cria um arquivo de middleware em **source/Middleware**

    php mx middleware [middlewareRef]

**server**: Executa o servidor embutido do PHP

    php mx server

## Config

**BASE_URL**: Url base utilizadas em chamada via terminal

    BASE_URL = http:127.0.0.1:8888

**CACHE**: Tempo de cache

    CACHE = null|true //Utiliza a configuração de cache global
    CACHE = 0|false //Bloqueia cache
    CACHE = +1 days //Utiliza um cache de 1 dia
    CACHE = +30 days //Utiliza um cache de 30 dia

**CACHE_EXEMPLE**: Tempo de cache para arquivos de uma extensão [.exemple] em horas

    CACHE_JPG = 672 //Cache para arquivo .jpg
    CACHE_ICO = 672 //Cache para arquivo .ico
    CACHE_ZIP = 24 //Cache para arquivo .zip
    CACHE_PDF = 12 //Cache para arquivo .pdf
    CACHE_...

**COOKIE_LIFE**: O tempo de vida do cookie em strtotime

    COOKIE_LIFE = '+1 days'

**COOKIE_DOMAIN**: Dominio para ser usado na criação de cookies

    COOKIE_DOMAIN = ''

**JWT**: Chave padrão para strigns jwt

    JWT = elegance-jwt-default-key

**PORT**: Porta para utilização do servidor embutido

    PORT = 8888
    
**STM_200**: Mensagem para o status 200

    STM_200 = ok

**STM_201**: Mensagem para o status 201

    STM_201 = created

**STM_303**: Mensagem para o status 303

    STM_303 = redirect

**STM_400**: Mensagem para o status 400

    STM_400 = bad request

**STM_401**: Mensagem para o status 401

    STM_401 = unauthorized

**STM_403**: Mensagem para o status 403

    STM_403 = forbidden

**STM_404**: Mensagem para o status 404

    STM_404 = not found

**STM_405**: Mensagem para o status 405

    STM_405 = method not allowed

**STM_500**: Mensagem para o status 500

    STM_500 = internal server error

**STM_501**: Mensagem para o status 501

    STM_501 = not implemented

**STM_503**: Mensagem para o status 503

    STM_503 = service unavailable

# Constant

 - **IS_GET**: Se a requisição é do tipo GET

 - **IS_POST**: Se a requisição é do tipo POST

 - **IS_PUT**: Se a requisição é do tipo PUT

 - **IS_PATCH**: Se a requisição é do tipo PATCH

 - **IS_DELETE**: Se a requisição é do tipo DELETE

 - **IS_OPTIONS**: Se a requisição é do tipo OPTIONS

 - **STS_OK**: Sucesso

 - **STS_CREATED**: Criado

 - **STS_NOT_CONTENT**: Nenhum conteúdo

 - **STS_REDIRECT**: Redirecionamento

 - **STS_BAD_REQUEST**: Sintaxe intorreta

 - **STS_UNAUTHORIZED**: Requer permissão

 - **STS_FORBIDDEN**: Proibido

 - **STS_NOT_FOUND**: Não encontrado

 - **STS_METHOD_NOT_ALLOWED**: Método não permitido

 - **STS_INTERNAL_SERVER_ERROR**: Erro interno do servidor

 - **STS_NOT_IMPLEMENTED**: Não implementado

 - **STS_SERVICE_UNAVAILABLE**: Indisponível

## Function
    
**url:**: Retorna uma string de URL

    url(...$params): string

**redirect**: Lança uma exception de redirecionamento

    redirect(...$params): never

**view:**: Renderiza uma view baseando em uma referencia de arquivo

    view(string $ref, array $data = []): string