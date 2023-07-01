<?php

namespace App\Http\Controllers;

use App\Jobs\SendQuoteJob;
use App\Services\NasdaqClient;
use App\Services\RapidApiClient;
use App\Http\Requests\HistoricalQuotesRequest;

class ReportController extends Controller
{
    public function getForm(NasdaqClient $nasdaqClient)
    {
        $companyListings = $nasdaqClient->getListings();

        return view('form', compact('companyListings'));
    }

    public function getQuotes(HistoricalQuotesRequest $request, RapidApiClient $rapidApiClient)
    {
        $symbol       = $request->get('symbol');
        $startDate    = $request->get('start-date');
        $endDate      = $request->get('end-date');
        $emailAddress = $request->get('email');

        try {
            $historicalData = $rapidApiClient->getHistoricalData($symbol, $startDate, $endDate);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        if (empty($historicalData)) {
            return redirect()->back()->withErrors('No data found for the given symbol and date range');
        }

        dispatch(new SendQuoteJob($emailAddress, $historicalData, $symbol, $startDate, $endDate));

        return view('quotes', compact('historicalData', 'symbol'));
    }
}
