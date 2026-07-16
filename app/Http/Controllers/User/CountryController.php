<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use App\Services\WeatherImportService;
use App\Services\CurrencyImportService;
use App\Services\NewsImportService;
use App\Services\RiskEngineService;
use Illuminate\View\View;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function index(
        \Illuminate\Http\Request $request,
        CountryService $countryService
    ): View {
        return view('user.countries.index', [
            'countries' => $countryService->getCountries($request),
            'regions' => \App\Models\Country::select('region')
                            ->distinct()
                            ->orderBy('region')
                            ->pluck('region'),
        ]);
    }

    public function show(Country $country)
    {
        $country->load([
            'riskScore',
            'weatherCaches',
            'currencyCaches',
            'newsCaches',
            'ports',
        ]);

        return view('user.countries.show', [
            'country' => $country,
        ]);
    }

    /**
     * Refresh data (Weather, Currency, News, Risk) for a specific country.
     */
    public function refresh(
        Country $country,
        WeatherImportService $weatherService,
        CurrencyImportService $currencyService,
        NewsImportService $newsService,
        RiskEngineService $riskService
    ) {
        try {
            Log::info('Triggering manual refresh for country', ['country' => $country->name]);

            // 1. Refresh Weather
            try {
                $weatherService->importForCountry($country, true);
            } catch (\Throwable $e) {
                Log::warning('Manual Weather Refresh failed', ['country' => $country->name, 'error' => $e->getMessage()]);
            }

            // 2. Refresh Currency
            try {
                $currencyService->importForCountry($country);
            } catch (\Throwable $e) {
                Log::warning('Manual Currency Refresh failed', ['country' => $country->name, 'error' => $e->getMessage()]);
            }

            // 3. Refresh News
            try {
                $newsService->importForCountry($country);
            } catch (\Throwable $e) {
                Log::warning('Manual News Refresh failed', ['country' => $country->name, 'error' => $e->getMessage()]);
            }

            // 4. Recalculate Risk Score
            try {
                $riskService->save($country);
            } catch (\Throwable $e) {
                Log::warning('Manual Risk Score recalculation failed', ['country' => $country->name, 'error' => $e->getMessage()]);
            }

            return redirect()->route('countries.show', $country)
                ->with('success', 'Country data has been refreshed successfully.');
        } catch (\Throwable $e) {
            Log::error('Country data refresh failed completely', [
                'country' => $country->name,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('countries.show', $country)
                ->with('error', 'Failed to refresh country data: ' . $e->getMessage());
        }
    }
}