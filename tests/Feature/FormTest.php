<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Company;
use App\Jobs\SendQuoteJob;
use App\Services\RapidApiClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class FormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['*' => Http::response($this->getJSONFixture('company-listings.json'), 200, ['Headers'])]);

        Artisan::call('app:import-companies');
    }

    public function test_the_index_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_company_symbols_are_cached()
    {
        $companies = Company::all()->pluck('name', 'symbol')->toArray();

        $this->get('/');

        $this->assertNotNull(cache('companies'));
        $this->assertEquals(cache('companies'), $companies);
    }

    /**
     * @dataProvider formDataProvider
     */
    public function testFormValidation($formData, $expectedErrors)
    {
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
