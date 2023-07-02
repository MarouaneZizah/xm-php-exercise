<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Jobs\SendQuoteJob;
use App\Services\RapidApiClient;
use App\Http\Requests\HistoricalQuotesRequest;

class ReportController extends Controller
{
    public function getForm()
    {
        $companies = Company::all();

        return view('form', compact('companies'));
    }

    public function getQuotes(HistoricalQuotesRequest $request, RapidApiClient $rapidApiClient)
    {
        $company      = Company::where('symbol', $request->get('symbol'))->first();
        $startDate    = $request->get('start-date');
        $endDate      = $request->get('end-date');
        $emailAddress = $request->get('email');

        try {
            $historicalData = $rapidApiClient->getHistoricalData($company->symbol, $startDate, $endDate);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        if (empty($historicalData)) {
            return redirect()->back()->withErrors('No data found for the given symbol and date range');
        }

        dispatch(new SendQuoteJob($emailAddress, $historicalData, $company, $startDate, $endDate));

        return view('quotes', compact('historicalData', 'company'));
    }
}
