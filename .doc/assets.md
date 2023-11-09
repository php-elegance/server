# Assets

Cria respostas para arquivos fisicos

    use \Elegance\Assets;

**send**: Envia um arquivo como resposta da requisição

    Assets::send(string $path, array $allowTypes = []): never

---

**download**: Realiza o download de um arquivo como resposta da requisição

    Assets::download(string $path, array $allowTypes = []): never

---

**load**: Carrega um arquivo ou view na resposta da aplicação

    Assets::load(string $path, array $allowTypes = []): void
