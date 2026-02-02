<?php

namespace denisok94\helper\other\Services;

class PreviewParsing
{

    /**
     * @param string $youtube_url
     * @return string|null
     */
    public static function getYoutubeId(string $youtube_url): ?string
    {
        // http://youtu.be/dQw4w9WgXcQ
        // http://www.youtube.com/embed/dQw4w9WgXcQ
        // http://www.youtube.com/watch?v=dQw4w9WgXcQ
        // http://www.youtube.com/?v=dQw4w9WgXcQ
        // http://www.youtube.com/v/dQw4w9WgXcQ
        // http://www.youtube.com/e/dQw4w9WgXcQ
        // http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ
        // http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ
        // http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ
        // http://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcQ
        // http://youtube.com/shorts/-sIs2C7wvuU
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);
        if (isset($match[1])) {
            return $match[1];
        }
        // https://stackoverflow.com/questions/2936467/parse-youtube-video-id-using-preg-match
        return null;
    }

    /**
     * @param string $url
     * @return array
     */
    public static function getFileInfo(string $url): array
    {
        ob_start(); // Начинаем буферизацию
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Только заголовки
        curl_setopt($ch, CURLOPT_HEADER, true); // Включить заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; HorslyPreviewBot/1.0)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //  подавляем вывод
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$filename) {
            if (preg_match('/Content-Disposition:.*filename="?([^";]+)"?/i', $header, $matches)) {
                $filename = urldecode($matches[1]);
            }
            return strlen($header); // Обязательно
        });

        $filename = null;
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);

        ob_end_clean(); // Очищаем буфер после cURL

        if ($httpCode === 200) {
            return [
                'exists' => true,
                'content_type' => $contentType,
                'size_bytes' => (int)$contentLength,
                'size_kb' => round($contentLength / 1024, 2),
                'size_mb' => round($contentLength / (1024 * 1024), 2),
                'filename' => $filename
            ];
        } else {
            return [
                'exists' => false,
                'http_code' => $httpCode,
                'filename' => $filename
            ];
        }
    }

    //-----------------------

    
    public function getPreviewFromUrl($url): array
    {
        $result = [
            'url' => $url,
            'title' => null,
            'description' => null,
            'thumbnail_url' => null,
            'thumbnail_width' => null,
            'thumbnail_height' => null,
            'embed_url' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'oembed' => null
        ];

        // === Шаг 1: Проверяем HEAD ===
        $head = $this->fetchHead($url);
        if (!$head) {
            return $result; // Не удалось получить мета
        }

        $contentType = $head['content_type'] ?? '';
        $contentLength = $head['content_length'] ?? null;
        $finalUrl = $head['url'] ?? $url;

        // === Шаг 2: Это файл? (не HTML) ===
        if (!$this->isHtmlContentType($contentType)) {
            // Это НЕ HTML — обрабатываем как файл
            $fileName = $this->extractFileNameFromUrlOrHeader($finalUrl, $head['headers'] ?? []);
            $result['file_name'] = $fileName;
            $result['file_size'] = $contentLength ? (int)$contentLength : null;
            $result['file_type'] = $contentType;

            // Если это изображение — используем URL как превью
            if (preg_match('/^image\/(jpeg|png|gif|webp)/i', $contentType)) {
                $result['thumbnail_url'] = $finalUrl;
                // Можно попробовать получить размеры позже (опционально)
            }

            // Используем имя файла как заголовок
            if ($fileName) {
                $nameWithoutExt = preg_replace('/\.[^\.]+$/', '', $fileName);
                $nameWithoutExt = urldecode($nameWithoutExt);
                $result['title'] = $nameWithoutExt;
            }

            return $result;
        }

        // === Шаг 3: Это HTML — парсим как страницу ===
        $html = $this->fetchHtmlBody($finalUrl, 512 * 1024);
        if (!$html) {
            return $result;
        }

        // === oEmbed ===
        $oembedUrl = $this->extractOEmbedLink($html);
        if ($oembedUrl) {
            $oembedData = $this->fetchJson($oembedUrl);
            if ($oembedData && isset($oembedData['title'])) {
                $embed_url = null;
                if (isset($oembedData['html']) && !empty($oembedData['html'])) {
                    preg_match('/<iframe[^>]+src="([^">]+)"/i', $oembedData['html'], $matches);
                    $embed_url = $matches[1] ?? null;
                }

                $result['oembed'] = $oembedData;
                $result['title'] = $oembedData['title'] ?? null;
                $result['description'] = $oembedData['description'] ?? ($oembedData['author_url'] ?? null);
                $result['thumbnail_url'] = $oembedData['thumbnail_url'] ?? null;
                $result['thumbnail_width'] = $oembedData['thumbnail_width'] ?? null;
                $result['thumbnail_height'] = $oembedData['thumbnail_height'] ?? null;
                $result['embed_url'] = $embed_url ?? null;
                return $result;
            }
        }

        // === Мета-теги ===
        $result['title'] = $this->extractMetaTitle($html) ?: $this->extractTitleTag($html);
        $result['description'] = $this->extractMetaDescription($html);

        $ogImage = $this->extractOgImage($html);
        if ($ogImage) {
            // Преобразуем в абсолютный URL
            $result['thumbnail_url'] = $this->resolveUrl($ogImage, $url);

            // Ищем og:image:width / height
            $ogWidth = $this->extractMetaContent($html, 'og:image:width') ?: $this->extractMetaContent($html, 'twitter:image:width');
            $ogHeight = $this->extractMetaContent($html, 'og:image:height') ?: $this->extractMetaContent($html, 'twitter:image:height');

            if ($ogWidth && is_numeric($ogWidth)) {
                $result['thumbnail_width'] = (int)$ogWidth;
            }
            if ($ogHeight && is_numeric($ogHeight)) {
                $result['thumbnail_height'] = (int)$ogHeight;
            }
        } else {
            // Резерв: первая картинка на странице
            $firstImg = $this->extractFirstImage($html);
            if ($firstImg) {
                $result['thumbnail_url'] = $this->resolveUrl($firstImg, $url);
            }
        }

        return $result;
    }

    // === Вспомогательные методы ===

    protected function fetchHead($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);  // Только заголовки
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; HorslyPreviewBot/1.0)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($httpCode >= 300 || !$response) {
            return null;
        }

        $header = substr($response, 0, $headerSize);
        $headers = $this->parseHeaders($header);

        $contentType = $headers['content-type'][0] ?? '';
        $contentLength = $headers['content-length'][0] ?? null;

        return [
            'url' => $finalUrl,
            'content_type' => trim(explode(';', $contentType)[0]),
            'content_length' => $contentLength,
            'headers' => $headers
        ];
    }

    protected function fetchHtmlBody($url, $maxBytes = 65536)
    {
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RANGE, "0-$maxBytes");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; HorslyPreviewBot/1.0)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        ob_end_clean();

        if ($httpCode >= 300 || !$response) {
            return null;
        }

        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        if (preg_match('/^Content-Type:\s*([^;\r\n]+)/im', $header, $m)) {
            $contentType = strtolower(trim($m[1]));
            if (!preg_match('/^(text\/html|application\/xhtml\+xml)/', $contentType)) {
                return null;
            }
        }

        return $body ?: null;
    }

    protected function isHtmlContentType($contentType)
    {
        return (bool) preg_match('/^(text\/html|application\/xhtml\+xml)/', strtolower($contentType));
    }

    protected function extractFileNameFromUrlOrHeader($url, $headers)
    {
        // 1. Из Content-Disposition
        if (!empty($headers['content-disposition'])) {
            foreach ($headers['content-disposition'] as $disposition) {
                if (preg_match('/filename[^;=\n]*=(([\'"]).*?\2|[^;\n]*)/i', $disposition, $m)) {
                    return trim($m[1], '"\'');
                }
            }
        }

        // 2. Из URL
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $fileName = basename($path);
            return urldecode($fileName);
        }

        return null;
    }

    protected function parseHeaders($headerText)
    {
        $headers = [];
        $lines = preg_split('/\r?\n/', $headerText);
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $key = strtolower(trim($key));
                $value = trim($value);
                $headers[$key][] = $value;
            }
        }
        return $headers;
    }

    // === Остальные методы без изменений ===

    protected function extractOEmbedLink($html)
    {
        if (preg_match('/<link[^>]+rel=["\']alternate["\'][^>]+type=["\']application\/json\+oembed["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $m)) {
            return htmlspecialchars_decode($m[1], ENT_QUOTES);
        }
        return null;
    }

    protected function fetchJson($url)
    {
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; HorslyPreviewBot/1.0)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        ob_end_clean();

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        return json_decode($response, true) ?: null;
    }

    protected function extractMetaTitle($html)
    {
        return $this->extractMetaContent($html, 'og:title') ?: $this->extractMetaContent($html, 'twitter:title');
    }

    protected function extractMetaDescription($html)
    {
        return $this->extractMetaContent($html, 'description') ?:
            $this->extractMetaContent($html, 'og:description') ?:
            $this->extractMetaContent($html, 'twitter:description');
    }

    protected function extractOgImage($html)
    {
        return $this->extractMetaContent($html, 'og:image') ?:
            $this->extractMetaContent($html, 'og:image:src') ?:
            $this->extractMetaContent($html, 'twitter:image') ?:
            $this->extractMetaContent($html, 'twitter:image:src');
    }

    protected function extractMetaContent($html, $property)
    {
        $escapedProp = preg_quote($property, '/');

        // Ищем: property="og:image" content="..."
        if (preg_match('/<meta[^>]+property\s*=\s*["\']' . $escapedProp . '["\'][^>]*content\s*=\s*["\']([^"\']*)["\']/i', $html, $m)) {
            return htmlspecialchars_decode($m[1], ENT_QUOTES);
        }

        // Ищем: content="..." property="og:image"
        if (preg_match('/<meta[^>]+content\s*=\s*["\']([^"\']*)["\'][^>]*property\s*=\s*["\']' . $escapedProp . '["\']/i', $html, $m)) {
            return htmlspecialchars_decode($m[1], ENT_QUOTES);
        }

        // Ищем по name (для twitter:title и т.п.)
        if (preg_match('/<meta[^>]+name\s*=\s*["\']' . $escapedProp . '["\'][^>]*content\s*=\s*["\']([^"\']*)["\']/i', $html, $m)) {
            return htmlspecialchars_decode($m[1], ENT_QUOTES);
        }

        if (preg_match('/<meta[^>]+content\s*=\s*["\']([^"\']*)["\'][^>]*name\s*=\s*["\']' . $escapedProp . '["\']/i', $html, $m)) {
            return htmlspecialchars_decode($m[1], ENT_QUOTES);
        }

        return null;
    }

    protected function extractTitleTag($html)
    {
        if (preg_match('/<title\b[^>]*>\s*(.+?)\s*<\/title>/is', $html, $m)) {
            return trim(strip_tags($m[1]));
        }
        return null;
    }

    protected function extractFirstImage($html)
    {
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            foreach ($matches[1] as $src) {
                if (!preg_match('/^(data:|javascript:)/i', $src)) {
                    return $src;
                }
            }
        }
        return null;
    }
    protected function resolveUrl($relative, $base)
    {
        // Удаляем хеш и параметры из base, оставляем только путь
        $parsedBase = parse_url($base);

        if (!$parsedBase || !isset($parsedBase['scheme']) || !isset($parsedBase['host'])) {
            return $relative; // Не можем разобрать базу
        }

        $scheme = $parsedBase['scheme'];
        $host = $parsedBase['host'];
        $basePath = isset($parsedBase['path']) ? dirname($parsedBase['path']) : '';

        if (empty($basePath)) $basePath = '/';

        // Уже абсолютный?
        if (preg_match('~^https?://~i', $relative)) {
            return $relative;
        }

        // Протокол относительный? //example.com/...
        if (str_starts_with($relative, '//')) {
            return $scheme . ':' . $relative;
        }

        // Абсолютный путь? /img/...
        if (str_starts_with($relative, '/')) {
            return $scheme . '://' . $host . $relative;
        }

        // Относительный путь: img/...
        return $scheme . '://' . $host . rtrim($basePath, '/') . '/' . ltrim($relative, '/');
    }
}
