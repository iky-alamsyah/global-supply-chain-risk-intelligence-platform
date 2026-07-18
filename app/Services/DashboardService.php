<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use App\Models\CountryRiskScore;
use App\Models\NewsCache;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Dashboard summary.
     */
    public function getDashboardData(): array
    {
        // Get articles by category for chart
        $articlesByCategory = Article::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Ensure all categories have a value
        $categories = ['economy', 'trade', 'shipping', 'logistics'];
        $articleChartData = [];
        foreach ($categories as $cat) {
            $articleChartData[$cat] = $articlesByCategory[$cat] ?? 0;
        }

        return [
            'totalUsers' => User::count(),
            'totalPorts' => Port::count(),
            'totalArticles' => Article::count(),
            'publishedArticles' => Article::where('status', 'published')->count(),
            'draftArticles' => Article::where('status', 'draft')->count(),
            'archivedArticles' => Article::where('status', 'archived')->count(),
            'totalCountries' => Country::count(),
            'totalFavorites' => Favorite::count(),
            'highRisk' => CountryRiskScore::where('risk_level', 'HIGH')->count(),
            'mediumRisk' => CountryRiskScore::where('risk_level', 'MEDIUM')->count(),
            'lowRisk' => CountryRiskScore::where('risk_level', 'LOW')->count(),
            'articleChartData' => $articleChartData,
        ];
    }
}