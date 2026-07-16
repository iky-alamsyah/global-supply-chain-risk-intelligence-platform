<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComparisonController extends Controller
{
    /**
     * Display Comparison Dashboard.
     */
    public function index(Request $request): View
    {
        // Get all active countries for the selection panel
        $allCountries = Country::where('is_active', true)
            ->with('riskScore')
            ->orderBy('name')
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'iso2' => $c->iso2,
                    'iso3' => $c->iso3,
                    'currency_code' => $c->currency_code,
                    'region' => $c->region,
                    'flag' => $c->flag,
                    'risk_score' => $c->riskScore ? (float) $c->riskScore->risk_score : 0.0,
                ];
            });

        $selectedIds = $request->input('countries', []);
        $comparedCountries = collect();
        $highestRiskCountryId = null;
        $radarDatasets = collect();
        $barDatasets = collect();

        if (is_array($selectedIds) && count($selectedIds) >= 2) {
            $comparedCountries = Country::whereIn('id', $selectedIds)
                ->where('is_active', true)
                ->with(['riskScore', 'latestCurrency', 'latestWeather', 'statistics'])
                ->withCount('ports')
                ->get();

            // Find country with highest risk score
            $maxRisk = -1;
            foreach ($comparedCountries as $country) {
                $score = $country->riskScore ? (float) $country->riskScore->risk_score : 0;
                if ($score > $maxRisk) {
                    $maxRisk = $score;
                    $highestRiskCountryId = $country->id;
                }
            }

            // Predefined high-contrast colors
            $colors = [
                'rgba(99, 102, 241, 1)',   // Indigo
                'rgba(236, 72, 153, 1)',   // Pink
                'rgba(20, 184, 166, 1)',   // Teal
                'rgba(245, 158, 11, 1)',   // Amber
                'rgba(139, 92, 246, 1)',   // Violet
                'rgba(239, 68, 68, 1)'     // Red
            ];
            $bgColors = [
                'rgba(99, 102, 241, 0.15)',
                'rgba(236, 72, 153, 0.15)',
                'rgba(20, 184, 166, 0.15)',
                'rgba(245, 158, 11, 0.15)',
                'rgba(139, 92, 246, 0.15)',
                'rgba(239, 68, 68, 0.15)'
            ];

            foreach ($comparedCountries as $idx => $c) {
                $color = $colors[$idx % count($colors)];
                $bgColor = $bgColors[$idx % count($bgColors)];

                $radarDatasets->push([
                    'label' => $c->name,
                    'data' => [
                        (float) ($c->riskScore ? $c->riskScore->risk_score : 0.0),
                        (float) ($c->riskScore ? $c->riskScore->gdp_score : 0.0),
                        (float) ($c->riskScore ? $c->riskScore->weather_score : 0.0),
                        (float) ($c->riskScore ? $c->riskScore->currency_score : 0.0),
                        (float) ($c->riskScore ? $c->riskScore->news_score : 0.0),
                    ],
                    'borderColor' => $color,
                    'backgroundColor' => $bgColor,
                    'borderWidth' => 2,
                    'pointBackgroundColor' => $color,
                ]);

                // Retrieve temp and ports
                $temp = $c->latestWeather ? (float) $c->latestWeather->temperature : 0.0;
                $ports = (int) $c->ports_count;
                $totalRisk = $c->riskScore ? (float) $c->riskScore->risk_score : 0.0;

                $barDatasets->push([
                    'label' => $c->name,
                    'backgroundColor' => $color,
                    'data' => [$temp, $ports, $totalRisk],
                ]);
            }
        }

        return view('user.comparison.index', compact(
            'allCountries', 
            'comparedCountries', 
            'highestRiskCountryId',
            'radarDatasets',
            'barDatasets'
        ));
    }
}