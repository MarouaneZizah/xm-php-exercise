<?php

namespace tests\Unit\Services;

use Exception;
use Tests\TestCase;
use App\Services\NasdaqClient;
use Illuminate\Support\Facades\Http;

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

        $nasdaqClient = new NasdaqClient();

        $listings = $nasdaqClient->getCompanies();

        $this->assertEquals($listings, [
            [
                'name'   => 'Company A',
                'symbol' => 'A',
            ],
            [
                'name'   => 'Company B',
                'symbol' => 'B',
            ],
        ]);
    }

    public function test_exception_is_thrown_if_request_fails()
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to get Nasdaq listings from API');

        $nasdaqClient = new NasdaqClient();

        $nasdaqClient->getCompanies();
    }
}
