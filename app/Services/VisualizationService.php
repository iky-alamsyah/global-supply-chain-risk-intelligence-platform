<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;
use App\Models\Port;

class VisualizationService
{
    public function getDashboardData(): array
    {
        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $countries = Country::with([
            'riskScore',
            'ports',
            'weatherCaches',
            'currencyCaches',
        ])->get();

        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        $totalCountries = $countries->count();

        $totalPorts = Port::count();

        $totalNews = NewsCache::count();

        $averageRisk = round(

            $countries
                ->filter(fn($country) => $country->riskScore)
                ->avg(fn($country) => $country->riskScore->risk_score),

            2

        );

        /*
        |--------------------------------------------------------------------------
        | Risk Distribution
        |--------------------------------------------------------------------------
        */

        $high = $countries
            ->where('riskScore.risk_level', 'HIGH')
            ->count();

        $medium = $countries
            ->where('riskScore.risk_level', 'MEDIUM')
            ->count();

        $low = $countries
            ->where('riskScore.risk_level', 'LOW')
            ->count();


        /*
|--------------------------------------------------------------------------
| Top 10 High Risk Countries
|--------------------------------------------------------------------------
*/

$topRisk = $countries
    ->filter(fn ($country) => $country->riskScore)
    ->sortByDesc(fn ($country) => $country->riskScore->risk_score)
    ->take(10)
    ->values();

    /*
|--------------------------------------------------------------------------
| Top 10 Countries by Ports
|--------------------------------------------------------------------------
*/

$topPorts = Country::withCount('ports')
    ->orderByDesc('ports_count')
    ->take(10)
    ->get();

    /*
|--------------------------------------------------------------------------
| News Category Distribution
|--------------------------------------------------------------------------
*/

$newsCategory = NewsCache::selectRaw('category, COUNT(*) as total')
    ->groupBy('category')
    ->orderByDesc('total')
    ->get();


    /*
|--------------------------------------------------------------------------
| Average Temperature by Region
|--------------------------------------------------------------------------
*/

$weatherRegion = $countries
    ->groupBy('region')
    ->map(function ($items) {

        $temperatures = $items
            ->map(function ($country) {

                return optional(
                    $country->weatherCaches
                        ->sortByDesc('created_at')
                        ->first()
                )->temperature;

            })
            ->filter();

        return $temperatures->count()
            ? round($temperatures->avg(), 1)
            : 0;
    });

        return compact(

            'countries',

            'totalCountries',

            'totalPorts',

            'totalNews',

            'averageRisk',

            'high',

            'medium',

            'low',

            'topRisk',

            'topPorts',

'newsCategory',

'weatherRegion'

        );
    }
}