<?php

namespace tests\Unit\Services;

use Exception;
use Tests\TestCase;
use App\Services\NasdaqClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NasdaqClientTest extends TestCase
{
    public function test_get_nasdaq_listings()
    {
        $responseFixture = [
            [
                'Company Name' => 'Company A',
                'Symbol'       => 'A',
            ],
            [
                'Company Name' => 'Company B',
                'Symbol'       => 'B',
            ],
        ];

        Http::fake([
            '*' => Http::response($responseFixture, 200, ['Headers']),
        ]);

        Cache::shouldReceive('remember')->with('nasdaq_list', 3600, \Closure::class)->andReturn($responseFixture);

        $nasdaqClient = new NasdaqClient();

        $listings = $nasdaqClient->getListings();

        $this->assertEquals($responseFixture, $listings);
    }

    public function test_get_cached_listings()
    {
        $responseFixture = [
            [
                'Company Name' => 'Company A',
                'Symbol'       => 'A',
            ],
            [
                'Company Name' => 'Company B',
                'Symbol'       => 'B',
            ],
        ];

        Cache::shouldReceive('put')->with('nasdaq_list', $responseFixture, 3600);

        Cache::put('nasdaq_list', $responseFixture, 3600);

        Cache::shouldReceive('remember')->with('nasdaq_list', 3600, \Closure::class)->andReturn($responseFixture);

        $nasdaqClient = new NasdaqClient();

        $listings = $nasdaqClient->getListings();

        $this->assertEquals($responseFixture, $listings);
    }

    public function test_exception_is_thrown_if_request_fails() {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to get Nasdaq listings from API');

        $nasdaqClient = new NasdaqClient();

        $nasdaqClient->getListings();
    }
}
