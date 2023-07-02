<?php

namespace App\Services;

use Exception;
use App\Models\Company;
use Illuminate\Support\Facades\Http;

class NasdaqClient
{
    /**
     * @throws Exception
     */
    public function getCompanies(): array
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
