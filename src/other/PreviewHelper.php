<?php

namespace denisok94\helper\other;

use RuntimeException, Throwable;
use Illuminate\Support\Facades\Http;
use denisok94\helper\other\Services\OEmbedProviderManager;
use denisok94\helper\other\Services\PreviewParsing as PP;

/**
 * Привью для ссылок в тексте
 */
class PreviewHelper
{
    /**
     * Проверка наличии ссылок в тексте
     */
    public static function isLink(string $text): bool
    {
        $pattern = '/https?:\/\/[^\s<>"{}|\\^`\[\]]+/i';
        preg_match_all($pattern, $text, $matches);
        $urls = $matches[0];
        return count($matches[0]) > 0;
    }

    /**
     * @param string $text
     * @return array
     */
    public static function getLinkData(string $text): array
    {
        try {
            $previews = PreviewHelper::analyzeLinks($text);

            return $previews;
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * @param string $text
     * @return array<array|array{domain: string|null, preview: array|null, type: string, url: string>
     */
    protected static function analyzeLinks(string $text): array
    {
        $pattern = '/https?:\/\/[^\s<>"{}|\\^`\[\]]+/i';
        preg_match_all($pattern, $text, $matches);
        $urls = array_unique($matches[0]);
        $results = [];
        $pp = new PP();
        $oEmbed = new OEmbedProviderManager();

        foreach ($urls as $url) {
            $isFile = $isYouTube = $isX = false;
            $type = 'site';
            $preview = null;
            try {
                $url = rtrim($url, '.;,!?');
                $parsed = parse_url($url);
                if (!isset($parsed['host'])) {
                    continue;
                }
                $host = strtolower($parsed['host']);
                $path = $parsed['path'] ?? '';
                $query = $parsed['query'] ?? '';
                $domain = preg_replace('/^www\./', '', $host);
                // -------------------------------
                // Определение типа
                // === YouTube ===
                if (strpos($domain, 'youtube.com') !== false || $domain === 'youtu.be') {
                    $isYouTube = true;
                    if (strpos($path, '/shorts/') === 0) {
                        $type = 'video'; // Shorts — это видео
                    } elseif (strpos($path, '/post/') === 0) {
                        $type = 'site';
                    } elseif (strpos($path, '/@') === 0) {
                        $type = 'site'; // @username
                    } elseif (strpos($path, '/live/') === 0) {
                        $type = 'video'; // Прямой эфир
                    } elseif ($domain === 'youtu.be' || strpos($path, '/watch') === 0 || strpos($path, '/embed/') === 0 || strpos($path, '/v/') === 0) {
                        $type = 'video';
                    } else {
                        $type = 'site';
                    }
                }
                // === Vimeo ===
                elseif (strpos($domain, 'vimeo.com') !== false) {
                    // Vimeo: /album/, /channels/, /groups/, /videos/, или просто ID
                    if (preg_match('^/(\d+|video/\d+|ondemand)!', $path)) {
                        $type = 'video';
                    } elseif (preg_match('!/album/!', $path) || preg_match('!/channels/!', $path)) {
                        $type = 'site';
                    } else {
                        $type = 'video'; // vimeo.com/123456
                    }
                }
                // === Twitch ===
                elseif (strpos($domain, 'twitch.tv') !== false) {
                    if (preg_match('!/videos/!', $path)) {
                        $type = 'video';
                    } elseif (preg_match('!/clip/!', $path)) {
                        $type = 'video';
                    } elseif (preg_match('!/([a-zA-Z0-9_]+)$!', $path)) {
                        $type = 'video'; // прямой эфир
                    } else {
                        $type = 'site';
                    }
                }
                // === Dailymotion ===
                elseif (strpos($domain, 'dailymotion.com') !== false) {
                    if (preg_match('!/video/!', $path)) {
                        $type = 'video';
                    } elseif (preg_match('!/user/!', $path)) {
                        $type = 'site';
                    }
                }
                // === x / twitter ===
                elseif (
                    strpos($domain, 'vxtwitter.com') !== false ||
                    strpos($domain, 'twitter.com') !== false ||
                    strpos($domain, 'x.com') !== false
                ) {
                    $url = preg_replace('~^https?://vxtwitter\.com~', 'https://twitter.com', $url);
                    $isX = true;
                }
                // === остальное ===
                elseif (preg_match('/\.(jpe?g|png|gif|webp|bmp|svg)$/i', $url)) {
                    $isFile = true;
                    $type = 'image';
                } elseif (preg_match('/\.(mp4|webm|avi|mov|mkv|flv|3gp)$/i', $url)) {
                    $isFile = true;
                    $type = 'video';
                } elseif (preg_match('/\.(mp3|wav|ogg|flac|aac|m4a)$/i', $url)) {
                    $isFile = true;
                    $type = 'audio';
                } elseif (preg_match('/\.(pdf|docx?|xlsx?|pptx?|zip|rar|7z|tar|gz|exe|txt|csv)$/i', $url)) {
                    $isFile = true;
                    $type = 'file';
                }
                // -------------------------------
                // Получаем данные
                // === YouTube ===
                if ($isYouTube == true && $type == 'video') {
                    $youtube_id = $pp::getYoutubeId($url);
                    if ($youtube_id) {
                        $response = Http::withoutVerifying()
                            ->withOptions(['verify' => false, 'timeout' => 5, 'connect_timeout' => 5])
                            ->get('https://www.youtube.com/oembed', ['url' =>  'https://www.youtube.com/watch?v=' . $youtube_id, 'format' => 'json']);
                        if ($response->successful()) {
                            $oembed = $response->json();
                            $preview = [
                                'title' => $oembed['title'] ?? ($oembed['author_name'] ?? 'None title'),
                                'author_name' => $oembed['author_name'] ?? null,
                                'author_url' => $oembed['author_url'] ?? null,
                                'type' => $oembed['type'] ?? 'site',
                                'thumbnail_url' => $oembed['thumbnail_url'] ?? null,
                                'thumbnail_width' => $oembed['thumbnail_width'] ?? null,
                                'thumbnail_height' => $oembed['thumbnail_height'] ?? null,
                                'embed_url' => "https://www.youtube.com/embed/$youtube_id",
                            ];
                        }
                    }
                }
                // === для файлов ===
                elseif ($isFile == true && in_array($type, ['image', 'video', 'audio', 'file'])) {
                    try {
                        $filedata = $pp::getFileInfo($url);
                        if ($filedata['exists']) {
                            $filename = ($filedata['filename'] ?? urldecode(basename($url))) . " (" . $filedata['size_mb'] . "mb)";
                            $preview = [
                                'title' => $filename,
                                'embed_url' => in_array($type, ['image', 'video']) ? $url : null
                            ];
                            if ($type == 'image') {
                                $preview['thumbnail_url'] = $url;
                            }
                        }
                    } catch (Throwable $th) {
                        //throw $th;
                    }
                }
                // === остальное ===
                else {
                    // === Поиск в oEmbed-реестре ===
                    $providerEndpoint = $oEmbed->findProviderEndpoint($url);
                    // === oEmbed, если нашли endpoint ===
                    if ($providerEndpoint) {
                        try {
                            $response = Http::withoutVerifying()
                                ->withOptions(['verify' => false, 'timeout' => 5, 'connect_timeout' => 5])
                                ->get($providerEndpoint, ['url' => urlencode($url), 'format' => 'json']);
                            if ($response->successful()) {
                                $oembed = $response->json();
                                if (isset($oembed['title'])) {
                                    $embed_url = null;
                                    if (isset($oembed['html']) && !empty($oembed['html'])) {
                                        preg_match('/<iframe[^>]+src="([^">]+)"/i', $oembed['html'], $matches);
                                        $embed_url = $matches[1] ?? null;
                                    }

                                    $preview = [
                                        'title' => $oembed['title'] ?? ($oembed['author_name'] ?? 'None title'),
                                        'author_name' => $oembed['author_name'] ?? null,
                                        'author_url' => $oembed['author_url'] ?? null,
                                        'description' => $oembed['description'] ?? ($oembed['author_url'] ?? null),
                                        'type' => $oembed['type'] ?? 'site',
                                        'thumbnail_url' => $oembed['thumbnail_url'] ?? null,
                                        'thumbnail_width' => $oembed['thumbnail_width'] ?? null,
                                        'thumbnail_height' => $oembed['thumbnail_height'] ?? null,
                                        'embed_url' => $embed_url,
                                    ];
                                    if ($oembed['type'] === 'video') $type = 'video';
                                    if ($oembed['type'] === 'photo') $type = 'image';
                                }
                            }
                        } catch (Throwable $th) {
                            // === Попытка номер 2 ===
                            $result = $pp->getPreviewFromUrl($url);
                            if (isset($result['title']) && !empty($result['title'])) {
                                $preview = $result;
                            }
                        }
                    }
                    // === идм на сайт и парсим мета-теги ===
                    else {
                        $result = $pp->getPreviewFromUrl($url);
                        if (isset($result['title']) && !empty($result['title'])) {
                            $preview = $result;
                        }
                    }
                }

                $results[] = [
                    'url' => $url,
                    'domain' => $domain,
                    'type' => $type,
                    'preview' => $preview
                ];
            } catch (Throwable $th) {
                throw $th;
            }
        } // end foreach

        return $results;
    }
}
