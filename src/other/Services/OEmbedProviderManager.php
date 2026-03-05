<?php

namespace denisok94\helper\other\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class OEmbedProviderManager
{
    protected $filePath = 'oembed/providers.json';
    protected $url = 'https://oembed.com/providers.json';

    public function getProviders(): array
    {
        if (Storage::disk('local')->exists($this->filePath)) {
            $content = Storage::disk('local')->get($this->filePath);
            $providers = json_decode($content, true);

            if ($providers !== null) {
                return $providers;
            }
        }
        return $this->fetchAndStore();
    }

    protected function fetchAndStore(): array
    {
        $response = Http::timeout(10)->get($this->url);

        if ($response->successful()) {
            $providers = $response->json();

            if (is_array($providers)) {
                Storage::disk('local')->makeDirectory(dirname($this->filePath));
                Storage::disk('local')->put($this->filePath, $response->body());

                return $providers;
            }
        }
        return [];
    }

    public function findProviderEndpoint(string $url): ?string
    {
        $normalizedUrl = $this->normalizeUrl($url);
        $host = parse_url($normalizedUrl, PHP_URL_HOST);
        if (!$host) return null;
        try {
            $providers = $this->getProviders();

            foreach ($providers as $provider) {
                foreach ($provider['endpoints'] as $endpoint) {
                    if (isset($endpoint['schemes'])) {
                        foreach ($endpoint['schemes'] as $scheme) {
                            if ($this->matchesScheme($normalizedUrl, $scheme)) {
                                return $endpoint['url'];
                            }
                        }
                    }

                    if (isset($endpoint['discovery']) && $endpoint['discovery'] === true) {
                        $providerHost = parse_url($provider['provider_url'], PHP_URL_HOST);
                        if ($providerHost && ($host === $providerHost || 'www.' . $host === $providerHost || $host === 'www.' . $providerHost)) {
                            return $endpoint['url'];
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return null;
    }

    protected function matchesScheme(string $url, string $scheme): bool
    {
        try {
            $pattern = str_replace('*', '.*', preg_quote($scheme, '/'));
            $pattern = '/^' . $pattern . '$/i';
            if ((bool) preg_match($pattern, $url)) {
                return true;
            } else {
                return $this->matchesSchemeV2($url, $scheme);
            }
        } catch (\Throwable $th) {
            return false;
            //throw $th;
        }
    }

    protected function matchesSchemeV2(string $url, string $scheme): bool
    {
        // 1. Разбираем схему и URL
        $schemeParts = parse_url($scheme);
        $urlParts = parse_url($url);

        if (!$schemeParts || !$urlParts) {
            return false;
        }

        // 2. Проверяем протокол (https/http)
        if ($schemeParts['scheme'] !== $urlParts['scheme']) {
            return false;
        }

        // 3. Проверяем хост (с учётом wildcard *)
        if (!$this->hostMatches($urlParts['host'], $schemeParts['host'])) {
            return false;
        }

        // 4. Проверяем путь (с поддержкой *)
        $schemePath = $schemeParts['path'] ?? '';
        $urlPath = $urlParts['path'] ?? '';

        return $this->pathMatches($urlPath, $schemePath);
    }

    // Проверка совпадения хоста (с поддержкой *.twitter.com)
    protected function hostMatches(string $urlHost, string $schemeHost): bool
    {
        if ($urlHost === $schemeHost) {
            return true;
        }

        // Если схема содержит *.domain.com
        if (str_starts_with($schemeHost, '*.') && str_ends_with($schemeHost, '.com')) {
            $domain = substr($schemeHost, 2); // Убираем *.
            return str_ends_with($urlHost, $domain);
        }

        return false;
    }

    // Проверка пути (с поддержкой *)
    protected function pathMatches(string $urlPath, string $schemePath): bool
    {
        // $pattern = str_replace('*', '.*', $schemePath);
        // $pattern = '/^' . $pattern . '$/i';
        // return (bool)preg_match($pattern, $urlPath);

        // 1. Разбиваем путь по /, но сохраняем структуру
        $schemeSegments = explode('/', trim($schemePath, '/'));
        $urlSegments = explode('/', trim($urlPath, '/'));

        // 2. Если количество сегментов не совпадает (без учёта *), то false
        if (count($schemeSegments) !== count($urlSegments)) {
            return false;
        }

        // 3. Проверяем каждый сегмент
        foreach ($schemeSegments as $i => $segment) {
            if ($segment === '*') {
                continue; // * — любой сегмент
            }
            // Экранируем спецсимволы в сегменте (кроме *)
            $quoted = preg_quote($segment, '/');
            // Создаём шаблон для точного совпадения сегмента
            $pattern = '/^' . $quoted . '$/i';
            if (!preg_match($pattern, $urlSegments[$i])) {
                return false;
            }
        }
        return true;
    }

    protected function normalizeUrl(string $url): string
    {
        if (!preg_match('~^https?://~', $url)) {
            $url = 'https://' . $url;
        }
        return $url;
    }

    public function refresh(): array
    {
        return $this->fetchAndStore();
    }
}

