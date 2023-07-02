<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\NasdaqClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Companies from Nasdaq API.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
           $companyListings = (new NasdaqClient())->getCompanies();

            DB::table('companies')->truncate();

            Company::insert($companyListings);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            return Command::FAILURE;
        }
    }
}
