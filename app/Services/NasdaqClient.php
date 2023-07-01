<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NasdaqClient
{
    public function getListings()
    {
        return Cache::remember('nasdaq_list', 3600, function () {
            return $this->getNasdaqListedJson();
        });
    }

    /**
     * @throws Exception
     */
    private function getNasdaqListedJson(): array
    {
        $response = Http::get($this->getBaseUrl());

        if ($response->failed()) {
            throw new Exception('Failed to get Nasdaq listings from API');
        }

        $data = $response->json();

        return array_map(function ($item) {
            return [
                'name'   => $item['Company Name'],
                'symbol' => $item['Symbol'],
            ];
        }, $data);
    }

    private function getBaseUrl()
    {
        return config('services.nasdaq.url');
    }
}
