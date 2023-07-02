<?php

namespace tests\Unit\Commands;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ImportCompaniesTest extends TestCase
{
    public function test_command_succeeds()
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

        $this->artisan('app:import-companies')->assertExitCode(0);

        $this->assertDatabaseCount('companies', 2);

        $this->assertDatabaseHas('companies', [
            'name'   => 'Company A',
            'symbol' => 'A',
        ]);

        $this->assertDatabaseHas('companies', [
            'name'   => 'Company B',
            'symbol' => 'B',
        ]);
    }
}
