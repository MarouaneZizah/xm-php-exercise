<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\SendQuoteJob;
use App\Services\RapidApiClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class FormTest extends TestCase
{
    public function test_the_index_page_returns_a_successful_response(): void
    {
        Http::fake(['*' => Http::response($this->getJSONFixture('company-listings.json'), 200, ['Headers'])]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('nasdaq_list', 3600, \Closure::class)
            ->andReturn($this->getJSONFixture('company-listings-cached.json'));

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider formDataProvider
     */
    public function testFormValidation($formData, $expectedErrors)
    {
        Http::fake(['*' => Http::response($this->getJSONFixture('company-listings.json'), 200, ['Headers'])]);

        Cache::shouldReceive('remember')
            ->with('nasdaq_list', 3600, \Closure::class)
            ->andReturn($this->getJSONFixture('company-listings-cached.json'));

        $response = $this->post('/historical-quotes', $formData);

        $response->assertStatus(302)->assertSessionHasErrors($expectedErrors);
    }

    public function formDataProvider(): array
    {
        return [
            'missing_required_fields' => [
                [],
                [
                    'symbol',
                    'start-date',
                    'end-date',
                    'email',
                ],
            ],
            'invalid_symbol'          => [
                [
                    'symbol'     => 'INVALID_SYMBOL',
                    'start-date' => '06/01/2023',
                    'end-date'   => '06/02/2023',
                    'email'      => 'test@example.com',
                ],
                ['symbol'],
            ],
            'invalid_dates'           => [
                [
                    'symbol'     => 'AAPL',
                    'start-date' => '06/02/2023',
                    'end-date'   => '06/01/2023',
                    'email'      => 'test@example.com',
                ],
                [
                    'start-date',
                    'end-date',
                ],
            ],
            'invalid_email'           => [
                [
                    'symbol'     => 'AAPL',
                    'start-date' => '06/01/2023',
                    'end-date'   => '06/02/2023',
                    'email'      => 'invalid_email',
                ],
                ['email'],
            ],
        ];
    }

    public function test_successful_form_submit()
    {
        $quotes = [
            [
                'date'   => Carbon::today()->subDay(5)->timestamp,
                'open'   => 100.00,
                'high'   => 105.00,
                'low'    => 95.00,
                'close'  => 102.50,
                'volume' => 1000,
            ],
            [
                'date'   => Carbon::today()->subDay(3)->timestamp,
                'open'   => 102.50,
                'high'   => 110.00,
                'low'    => 101.00,
                'close'  => 108.50,
                'volume' => 1200,
            ],
            [
                'date'   => Carbon::today()->subDay(1)->timestamp,
                'open'   => 102.50,
                'high'   => 110.00,
                'low'    => 101.00,
                'close'  => 108.50,
                'volume' => 1200,
            ],
            [
                'date'   => Carbon::today()->timestamp,
                'open'   => 102.50,
                'high'   => 110.00,
                'low'    => 101.00,
                'close'  => 108.50,
                'volume' => 1200,
            ],
        ];

        $mockQuotesService = $this->mock(RapidApiClient::class);
        $mockQuotesService->shouldReceive('getHistoricalData')->once()->andReturn($quotes);

        Cache::shouldReceive('remember')
            ->once()
            ->with('nasdaq_list', 3600, \Closure::class)
            ->andReturn($this->getJSONFixture('company-listings-cached.json'));

        $symbol     = 'AAPL';
        $start_date = '06/01/2023';
        $end_date   = '06/02/2023';
        $email      = 'test@example.com';

        Queue::fake();

        $response = $this->post('/historical-quotes', [
            'symbol'     => $symbol,
            'start-date' => $start_date,
            'end-date'   => $end_date,
            'email'      => $email,
        ]);

        $response->assertStatus(200);

        Queue::assertPushed(SendQuoteJob::class);
    }
}
