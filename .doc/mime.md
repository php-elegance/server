# Mime

Captura e compara mimetypes de arquivos

    use Elegance\Mime;

---

**getExMime**: Retorna a extensão de um mimetype

    Mime::getExMime(string $mime): ?string

---

**getMimeEx**: Retorna o mimetype de uma extensão

    Mime::getMimeEx(string $ex): ?string

---

**getMimeFile**: Retorna o mimetype de um arquivo

    Mime::getMimeFile(string $file): ?string

---

**checkMimeEx**: Retorna verifica se uma extensão corresponde a algum mimetype fornecido

    Mime::checkMimeEx(string $ex, string ...$compare): bool

---

**checkMimeMime**: Retorna verifica se um mimetype corresponde a algum mimetype fornecido

    Mime::checkMimeMime(string $mime, string ...$compare): bool

---

**checkMimeFile**: Retorna verifica se um arquivo corresponde a algum mimetype fornecido

    Mime::checkMimeFile(string $file, string ...$compare): bool
