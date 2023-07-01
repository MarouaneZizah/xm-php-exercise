<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class RapidApiClient
{

    /**
     * @throws Exception
     */
    public function getHistoricalData(string $symbol, string $startDate, string $endDate): array
    {
        $response = Http::withHeaders([
            'x-rapidapi-key'  => config('services.rapidApi.key'),
            'x-rapidapi-host' => config('services.rapidApi.host'),
        ])->get($this->getBaseUrl('stock/v3/get-historical-data'), ['symbol' => $symbol]);

        if ($response->failed()) {
            throw new Exception('Failed to get historical data from API');
        }

        $prices = $response->json('prices');

        if (!$prices) {
            return [];
        }

        $filteredPrices = array_filter($prices, function ($price) use ($startDate, $endDate) {
            $date = Carbon::parse($price['date'])->format('Y-m-d H:i:s');

            return Carbon::parse($date)->between($startDate, $endDate);
        });

        return array_values(array_map(function ($price) {
            return [
                'date'   => array_key_exists('date', $price) ? Carbon::parse($price['date'])->format('Y-m-d') : null,
                'open'   => array_key_exists('open', $price) ? (float)number_format($price['open'], 2) : null,
                'high'   => array_key_exists('high', $price) ? (float)number_format($price['high'], 2) : null,
                'low'    => array_key_exists('low', $price) ? (float)number_format($price['low'], 2) : null,
                'close'  => array_key_exists('close', $price) ? (float)number_format($price['close'], 2) : null,
                'volume' => array_key_exists('volume', $price) ? $price['volume'] : null,
            ];
        }, $filteredPrices));
    }

    public function getBaseUrl($endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        $baseUrl  = rtrim(config('services.rapidApi.url'), '/');

        return "{$baseUrl}/{$endpoint}";
    }
}
