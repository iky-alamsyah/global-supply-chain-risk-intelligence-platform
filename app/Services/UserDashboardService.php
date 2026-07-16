<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryRiskScore;
use App\Models\NewsCache;

class UserDashboardService
{
    public function getDashboardData(): array
    {
        return [

            'totalCountries' => Country::count(),

            'highRisk' => CountryRiskScore::where('risk_level', 'HIGH')->count(),

            'mediumRisk' => CountryRiskScore::where('risk_level', 'MEDIUM')->count(),

            'lowRisk' => CountryRiskScore::where('risk_level', 'LOW')->count(),

            'topRiskCountries' => CountryRiskScore::with('country')
                ->orderByDesc('risk_score')
                ->take(5)
                ->get(),

            'latestNews' => NewsCache::with('country')
                ->latest('published_at')
                ->take(5)
                ->get(),
            
                'weatherAlerts' => \App\Models\WeatherAlert::with('country')
    ->latest('generated_at')
    ->take(10)
    ->get(),

'latestRates' => \App\Models\CurrencyCache::with('country')
    ->latest('rate_time')
    ->take(5)
    ->get(),

    'countriesMap' => \App\Models\CountryRiskScore::with('country')
    ->get()
    ->map(function ($risk) {

        return [

            'name' => $risk->country->name,

            'lat' => $risk->country->latitude,

            'lng' => $risk->country->longitude,

            'risk_score' => $risk->risk_score,

            'risk_level' => $risk->risk_level,

        ];

    }),
'riskDistribution' => [

    'high' => CountryRiskScore::where('risk_level', 'HIGH')->count(),

    'medium' => CountryRiskScore::where('risk_level', 'MEDIUM')->count(),

    'low' => CountryRiskScore::where('risk_level', 'LOW')->count(),

],

'riskByRegion' => CountryRiskScore::query()

    ->join('countries', 'country_risk_scores.country_id', '=', 'countries.id')

    ->selectRaw('countries.region, AVG(country_risk_scores.risk_score) as average_score')

    ->groupBy('countries.region')

    ->orderBy('average_score', 'desc')

    ->get(),
    
        ];
    }
}