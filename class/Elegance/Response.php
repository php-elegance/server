<?php

namespace Elegance;

abstract class Response
{
    protected static array $header = [];

    protected static ?int $status = null;
    protected static ?string $type = null;
    protected static mixed $content = null;

    protected static ?string $cache = null;

    protected static bool $download = false;
    protected static ?string $downloadName = null;

    /** Define o status HTTP da resposta */
    static function status(?int $status, bool $replace = true)
    {
        self::$status = $replace ? $status : (self::$status ?? $status);
    }

    /** Define um cabeçalho para a resposta */
    static function header(string|array $name, ?string $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $n => $v)
                self::header($n, $v);
        } else {
            self::$header[$name] = $value;
        }
    }

    /** Define o contentType da resposta */
    static function type(?string $type, bool $replace = true)
    {
        if ($type) {
            $type = trim($type, '.');
            $type = strtolower($type);
            $type = Mime::getMimeEx($type) ?? $type;
        }

        self::$type = $replace ? $type : (self::$type ?? $type);
    }

    /** Define o conteúdo da resposta */
    static function content(mixed $content, bool $replace = true)
    {
        self::$content = $replace ? $content : (self::$content ?? $content);
    }

    /** Define se o arquivo deve ser armazenado em cache */
    static function cache(?string $strToTime): void
    {
        self::$cache = $strToTime;
    }

    /** Define se o navegador deve fazer download da resposta */
    static function download(null|bool|string $download): void
    {
        if (is_string($download)) {
            self::$downloadName = $download;
            $download = true;
        }
        self::$download = boolval($download);
    }

    /** Envia a resposta ao navegador do cliente */
    static function send(): never
    {
        $content = self::getMontedContent();
        $headers = self::getMontedHeders();

        http_response_code(self::$status ?? STS_OK);

        foreach ($headers as $name => $value)
            header("$name: $value");

        die($content);
    }

    #==| GET |==#

    /** Retorna o status atual da resposta */
    static function getStatus(): ?int
    {
        return self::$status;
    }

    /** Retorna o conteúdo atual da resposta */
    static function getContent(): ?string
    {
        return self::$content;
    }

    /** Verifica se o tipo da resposta é um dos tipos informados */
    static function checkType(): bool
    {
        foreach (func_get_args() as $type)
            if (Mime::checkMimeMime($type, self::$type))
                return true;
        return false;
    }

    #==| Mount |==#

    /** Retorna conteúdo da resposta */
    protected static function getMontedContent(): string
    {
        return is_array(self::$content) ? json_encode(self::$content) : strval(self::$content);
    }

    /** Retorna cabeçalhos de resposta */
    protected static function getMontedHeders(): array
    {
        return [
            ...self::$header,
            ...self::getMontedHeaderCache(),
            ...self::getMontedHeaderType(),
            ...self::getMontedHeaderDownload(),
            ...self::getMontedHeaderElegance(),
        ];
    }

    /** Retorna os cabeçalhos do elegance */
    protected static function getMontedHeaderElegance(): array
    {
        return [
            'Elegance' => true,
            'Elegance-Status' => self::$status ?? STS_OK,
            'Elegance-Type' => Mime::getExMime(self::$type),
        ];
    }

    /** Retorna cabeçalhos de cache */
    protected static function getMontedHeaderCache(): array
    {
        if (!self::$type) self::type(is_json(self::$content) ? 'json' : 'html');

        $headerCache = [];

        $cacheType = Mime::getExMime(self::$type);

        $cacheTime = self::$cache ?? env(strtoupper("CACHE_$cacheType")) ?? env("CACHE");

        if ($cacheTime == '0') $cacheTime = false;

        $headerCache['Elegance-Cache'] = $cacheTime;

        if ($cacheTime) {
            $cacheTime = strtotime($cacheTime);
            $maxAge = time() - $cacheTime;
            $headerCache['Pragma'] = 'public';
            $headerCache['Cache-Control'] = "max-age=$maxAge";
            $headerCache['Expires'] = gmdate('D, d M Y H:i:s', $cacheTime) . ' GMT';
        } else {
            $headerCache['Pragma'] = 'no-cache';
            $headerCache['Cache-Control'] = 'no-cache, no-store, must-revalidat';
            $headerCache['Expires'] = '0';
        }

        return $headerCache ?? [];
    }

    /** Retorna cabeçalhos de tipo de conteúdo */
    protected static function getMontedHeaderType(): array
    {
        if (is_array(self::$content) || is_json(self::$content))
            self::type('json');

        $type = self::$type ?? Mime::getMimeEx('json');

        return ['Content-Type' => "$type; charset=utf-8"];
    }

    /** Retorna cabeçalhos de download */
    protected static function getMontedHeaderDownload(): array
    {
        $headerDownload = [];
        if (self::$download) {
            $ex = Mime::getExMime(self::$type) ?? 'download';
            $fileName = self::$downloadName ?? 'download';
            $fileName = File::setEx($fileName, $ex);
            $headerDownload['Content-Disposition'] = "attachment; filename=$fileName";
        }
        return $headerDownload;
    }
}
