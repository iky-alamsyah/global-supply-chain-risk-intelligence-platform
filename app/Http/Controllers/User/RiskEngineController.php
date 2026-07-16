<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CountryRiskScore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiskEngineController extends Controller
{
    /**
     * Display Risk Engine list and charts.
     */
    public function index(Request $request): View
    {
        $query = CountryRiskScore::with('country')
            ->join('countries', 'country_risk_scores.country_id', '=', 'countries.id')
            ->where('countries.is_active', true)
            ->select('country_risk_scores.*');

        // Search country
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('countries.name', 'like', "%{$search}%");
        }

        // Filter Risk Level
        if ($request->filled('level')) {
            $query->where('country_risk_scores.risk_level', $request->input('level'));
        }

        // Sort: Highest Risk (default), Lowest Risk
        $sort = $request->input('sort', 'highest');
        if ($sort === 'lowest') {
            $query->orderBy('country_risk_scores.risk_score', 'asc');
        } else {
            $query->orderBy('country_risk_scores.risk_score', 'desc');
        }

        $riskScores = $query->paginate(10)->withQueryString();

        // Get Top 10 for Chart.js mapped to simple arrays
        $chartData = CountryRiskScore::with('country')
            ->join('countries', 'country_risk_scores.country_id', '=', 'countries.id')
            ->where('countries.is_active', true)
            ->select('country_risk_scores.*')
            ->orderByDesc('risk_score')
            ->take(10)
            ->get()
            ->map(function ($s) {
                return [
                    'country' => $s->country->name,
                    'gdp' => (float) $s->gdp_score,
                    'weather' => (float) $s->weather_score,
                    'currency' => (float) $s->currency_score,
                    'news' => (float) $s->news_score,
                    'total' => (float) $s->risk_score,
                ];
            });

        return view('user.risk.index', compact('riskScores', 'chartData'));
    }
}