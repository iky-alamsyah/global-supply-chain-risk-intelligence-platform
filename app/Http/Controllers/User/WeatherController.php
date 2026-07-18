<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\WeatherImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class WeatherController extends Controller
{
    /**
     * Display Weather Dashboard.
     */
    public function index(Request $request): View
    {
        $query = Country::where('is_active', true)
            ->with(['latestWeather', 'weatherAlerts']);

        // Search Country
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso3', 'like', "%{$search}%")
                  ->orWhere('capital', 'like', "%{$search}%");
            });
        }

        // Filter Region
        if ($request->filled('region')) {
            $query->where('region', $request->input('region'));
        }

        $countries = $query->orderBy('name')->paginate(8)->withQueryString();
        
        $regions = Country::where('is_active', true)
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort()
            ->all();

        return view('user.weather.index', compact('countries', 'regions'));
    }

    /**
     * Refresh all weather data.
     */
    public function refresh(Request $request): RedirectResponse
    {
        $countries = Country::where('is_active', true)->get();

        foreach ($countries as $country) {
            \App\Jobs\RefreshWeatherJob::dispatchSync($country, true);
        }

        Log::info("Manual batch weather refresh completed synchronously for " . $countries->count() . " countries.");

        return redirect()->back()->with('success', 'All weather data has been successfully refreshed from Open-Meteo.');
    }
}