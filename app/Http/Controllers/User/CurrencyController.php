<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CurrencyCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Display Currency Dashboard with filters and analytics.
     */
    public function index(Request $request): View
    {
        $query = Country::where('is_active', true)
            ->with(['latestCurrency']);

        // Search Country or Currency
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('currency_code', 'like', "%{$search}%");
            });
        }

        // Filter Region
        if ($request->filled('region')) {
            $query->where('region', $request->input('region'));
        }

        // Filter Currency Code
        if ($request->filled('currency_code')) {
            $query->where('currency_code', $request->input('currency_code'));
        }

        // Sort Exchange Rate / Risk Score
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'rate_asc') {
                $query->leftJoin('currency_cache', function($join) {
                    $join->on('countries.id', '=', 'currency_cache.country_id')
                         ->whereRaw('currency_cache.id = (select id from currency_cache where country_id = countries.id order by created_at desc limit 1)');
                })
                ->select('countries.*')
                ->orderBy('currency_cache.exchange_rate', 'asc');
            } elseif ($sort === 'rate_desc') {
                $query->leftJoin('currency_cache', function($join) {
                    $join->on('countries.id', '=', 'currency_cache.country_id')
                         ->whereRaw('currency_cache.id = (select id from currency_cache where country_id = countries.id order by created_at desc limit 1)');
                })
                ->select('countries.*')
                ->orderBy('currency_cache.exchange_rate', 'desc');
            } elseif ($sort === 'risk_desc') {
                $query->leftJoin('currency_cache', function($join) {
                    $join->on('countries.id', '=', 'currency_cache.country_id')
                         ->whereRaw('currency_cache.id = (select id from currency_cache where country_id = countries.id order by created_at desc limit 1)');
                })
                ->select('countries.*')
                ->orderBy('currency_cache.currency_risk_score', 'desc');
            } elseif ($sort === 'risk_asc') {
                $query->leftJoin('currency_cache', function($join) {
                    $join->on('countries.id', '=', 'currency_cache.country_id')
                         ->whereRaw('currency_cache.id = (select id from currency_cache where country_id = countries.id order by created_at desc limit 1)');
                })
                ->select('countries.*')
                ->orderBy('currency_cache.currency_risk_score', 'asc');
            }
        } else {
            $query->orderBy('name');
        }

        $countries = $query->paginate(10)->withQueryString();

        // Get filter options
        $regions = Country::where('is_active', true)
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort()
            ->all();

        $currencyCodes = Country::where('is_active', true)
            ->whereNotNull('currency_code')
            ->distinct()
            ->pluck('currency_code')
            ->sort()
            ->all();

        // Analytics data
        $appreciation = CurrencyCache::with('country')
            ->whereNotNull('change_percentage')
            ->orderByDesc('change_percentage')
            ->take(5)
            ->get();

        $depreciation = CurrencyCache::with('country')
            ->whereNotNull('change_percentage')
            ->orderBy('change_percentage')
            ->take(5)
            ->get();

        $highestRisk = CurrencyCache::with('country')
            ->orderByDesc('currency_risk_score')
            ->take(5)
            ->get();

        $lowestRisk = CurrencyCache::with('country')
            ->orderBy('currency_risk_score')
            ->take(5)
            ->get();

        $mostStable = CurrencyCache::with('country')
            ->whereNotNull('change_percentage')
            ->selectRaw('*, ABS(change_percentage) as abs_change')
            ->orderBy('abs_change')
            ->take(5)
            ->get();

        $mostVolatile = CurrencyCache::with('country')
            ->whereNotNull('change_percentage')
            ->selectRaw('*, ABS(change_percentage) as abs_change')
            ->orderByDesc('abs_change')
            ->take(5)
            ->get();

        return view('user.currency.index', compact(
            'countries', 
            'regions', 
            'currencyCodes',
            'appreciation',
            'depreciation',
            'highestRisk',
            'lowestRisk',
            'mostStable',
            'mostVolatile'
        ));
    }
}