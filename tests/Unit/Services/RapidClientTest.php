<?php

namespace tests\Unit\Services;

use Exception;
use Carbon\Carbon;
use Tests\TestCase;
use App\Services\RapidApiClient;
use Illuminate\Support\Facades\Http;

class RapidClientTest extends TestCase
{
    public function test_get_historical_quotes()
    {
        Http::fake([
            '*' => Http::response([
                'prices' => [
                    [
                        'date' => Carbon::today()->subDay(5)->timestamp,
                        'open' => 100.00,
                        'high' => 105.00,
                        'low' => 95.00,
                        'close' => 102.50,
                        'volume' => 1000,
                    ],
                    [
                        'date' => Carbon::today()->subDay(3)->timestamp,
                        'open' => 102.50,
                        'high' => 110.00,
                        'low' => 101.00,
                        'close' => 108.50,
                        'volume' => 1200,
                    ],
                    [
                        'date' => Carbon::today()->subDay(1)->timestamp,
                        'open' => 102.50,
                        'high' => 110.00,
                        'low' => 101.00,
                        'close' => 108.50,
                        'volume' => 1200,
                    ],
                    [
                        'date' => Carbon::today()->timestamp,
                        'open' => 102.50,
                        'high' => 110.00,
                        'low' => 101.00,
                        'close' => 108.50,
                        'volume' => 1200,
                    ],
                ],
            ]),
        ]);

        $rapidApiClient = new RapidApiClient();

        $symbol = 'AAPL';
        $startDate = Carbon::today()->subDay(3)->format('m/d/Y');
        $endDate = Carbon::today()->format('m/d/Y');
        $result = $rapidApiClient->getHistoricalData($symbol, $startDate, $endDate);

        $expectedResult = [
            [
                'date' => Carbon::today()->subDay(3)->format('Y-m-d'),
                'open' => 102.50,
                'high' => 110.00,
                'low' => 101.00,
                'close' => 108.50,
                'volume' => 1200,
            ],
            [
                'date' => Carbon::today()->subDay(1)->format('Y-m-d'),
                'open' => 102.50,
                'high' => 110.00,
                'low' => 101.00,
                'close' => 108.50,
                'volume' => 1200,
            ],
            [
                'date' => Carbon::today()->format('Y-m-d'),
                'open' => 102.50,
                'high' => 110.00,
                'low' => 101.00,
                'close' => 108.50,
                'volume' => 1200,
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_historical_date_exception_thrown()
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $rapidApiClient = new RapidApiClient();

        $this->expectException(Exception::class);

        $symbol = 'AAPL';
        $startDate = '2023-06-01';
        $endDate = '2023-06-02';
        $rapidApiClient->getHistoricalData($symbol, $startDate, $endDate);
    }
}
