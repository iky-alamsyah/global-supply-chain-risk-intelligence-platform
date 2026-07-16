<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use App\Models\CountryRiskScore;
use App\Models\NewsCache;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;

class DashboardService
{
    /**
     * Dashboard summary.
     */
    public function getDashboardData(): array
    {
        return [

            'totalUsers' => User::count(),

            'totalPorts' => Port::count(),

            'totalArticles' => Article::count(),

            'totalCountries' => Country::count(),

            'totalNews' => NewsCache::count(),

            'highRisk' => CountryRiskScore::where(
                'risk_level',
                'HIGH'
            )->count(),

            'mediumRisk' => CountryRiskScore::where(
                'risk_level',
                'MEDIUM'
            )->count(),

            'lowRisk' => CountryRiskScore::where(
                'risk_level',
                'LOW'
            )->count(),

            'topRiskCountries' => CountryRiskScore::with('country')
                ->orderByDesc('risk_score')
                ->limit(10)
                ->get(),

            'latestNews' => NewsCache::with('country')
                ->latest('published_at')
                ->limit(10)
                ->get(),

        ];
    }
}