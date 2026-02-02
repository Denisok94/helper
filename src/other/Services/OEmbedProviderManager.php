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

    public function refresh(): array
    {
        return $this->fetchAndStore();
    }
}
