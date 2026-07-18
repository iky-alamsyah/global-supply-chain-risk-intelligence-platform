<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryStatistic;
use App\Models\CountryRiskScore;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the countries.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $regionFilter = $request->input('region');
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        // Build query using join to support ordering by risk_score
        $query = Country::select('countries.*')
            ->leftJoin('country_risk_scores', 'countries.id', '=', 'country_risk_scores.country_id')
            ->with(['ports', 'riskScore', 'weatherCaches']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('countries.name', 'like', "%{$search}%")
                  ->orWhere('countries.official_name', 'like', "%{$search}%")
                  ->orWhere('countries.iso2', 'like', "%{$search}%")
                  ->orWhere('countries.iso3', 'like', "%{$search}%")
                  ->orWhere('countries.region', 'like', "%{$search}%")
                  ->orWhere('countries.capital', 'like', "%{$search}%");
            });
        }

        if ($regionFilter) {
            $query->where('countries.region', $regionFilter);
        }

        if ($sort === 'risk_score') {
            $query->orderBy('country_risk_scores.risk_score', $direction);
        } else {
            $query->orderBy('countries.name', $direction);
        }

        $countries = $query->paginate(10)->withQueryString();

        // Get regions for filter dropdown
        $regions = Country::whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        return view('admin.countries.index', compact('countries', 'regions', 'search', 'regionFilter', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new country.
     */
    public function create(): View
    {
        return view('admin.countries.create');
    }

    /**
     * Store a newly created country in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'official_name' => ['nullable', 'string', 'max:255'],
            'iso2' => ['required', 'string', 'size:2', 'unique:countries,iso2'],
            'iso3' => ['required', 'string', 'size:3', 'unique:countries,iso3'],
            'region' => ['required', 'string', 'max:255'],
            'capital' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'population' => ['nullable', 'integer', 'min:0'],
            'gdp' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request) {
            $country = Country::create([
                'name' => $request->input('name'),
                'official_name' => $request->input('official_name'),
                'iso2' => strtoupper($request->input('iso2')),
                'iso3' => strtoupper($request->input('iso3')),
                'region' => $request->input('region'),
                'capital' => $request->input('capital'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'population' => $request->input('population'),
                'is_active' => true,
            ]);

            // Save GDP under Country Statistics
            if ($request->filled('gdp')) {
                CountryStatistic::create([
                    'country_id' => $country->id,
                    'year' => (int) date('Y'),
                    'gdp' => (float) $request->input('gdp'),
                    'data_source' => 'Manual Input',
                ]);
            }
        });

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully.');
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country): View
    {
        $country->load([
            'riskScore',
            'weatherCaches',
            'currencyCaches',
            'newsCaches',
            'ports',
        ]);

        return view('admin.countries.show', compact('country'));
    }

    /**
     * Show the form for editing the specified country.
     */
    public function edit(Country $country): View
    {
        $latestStat = $country->statistics()->latest('year')->first();
        $gdp = $latestStat ? $latestStat->gdp : null;

        return view('admin.countries.edit', compact('country', 'gdp'));
    }

    /**
     * Update the specified country in database.
     */
    public function update(Request $request, Country $country): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'official_name' => ['nullable', 'string', 'max:255'],
            'iso2' => ['required', 'string', 'size:2', Rule::unique('countries', 'iso2')->ignore($country->id)],
            'iso3' => ['required', 'string', 'size:3', Rule::unique('countries', 'iso3')->ignore($country->id)],
            'region' => ['required', 'string', 'max:255'],
            'capital' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'population' => ['nullable', 'integer', 'min:0'],
            'gdp' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $country) {
            $country->update([
                'name' => $request->input('name'),
                'official_name' => $request->input('official_name'),
                'iso2' => strtoupper($request->input('iso2')),
                'iso3' => strtoupper($request->input('iso3')),
                'region' => $request->input('region'),
                'capital' => $request->input('capital'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'population' => $request->input('population'),
            ]);

            // Save GDP under Country Statistics
            if ($request->filled('gdp')) {
                CountryStatistic::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'year' => (int) date('Y'),
                    ],
                    [
                        'gdp' => (float) $request->input('gdp'),
                        'data_source' => 'Manual Input',
                    ]
                );
            }
        });

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified country from database.
     */
    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();

        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }

    /**
     * Export countries to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Country::select('countries.*')
            ->leftJoin('country_risk_scores', 'countries.id', '=', 'country_risk_scores.country_id')
            ->with(['ports', 'riskScore']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('countries.name', 'like', "%{$search}%")
                  ->orWhere('countries.official_name', 'like', "%{$search}%")
                  ->orWhere('countries.iso2', 'like', "%{$search}%")
                  ->orWhere('countries.iso3', 'like', "%{$search}%")
                  ->orWhere('countries.region', 'like', "%{$search}%")
                  ->orWhere('countries.capital', 'like', "%{$search}%");
            });
        }

        if ($regionFilter = $request->input('region')) {
            $query->where('countries.region', $regionFilter);
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        if ($sort === 'risk_score') {
            $query->orderBy('country_risk_scores.risk_score', $direction);
        } else {
            $query->orderBy('countries.name', $direction);
        }

        $countries = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=countries_export_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Flag/Emoji', 'Country Name', 'Official Name', 'ISO2', 'ISO3', 'Region', 'Capital', 'Population', 'GDP (Current USD)', 'Risk Score', 'Total Ports'];

        $callback = function() use($countries, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($countries as $c) {
                $latestStat = $c->statistics()->latest('year')->first();
                $gdp = $latestStat ? $latestStat->gdp : null;

                fputcsv($file, [
                    $c->flag ?? '🌍',
                    $c->name,
                    $c->official_name,
                    $c->iso2,
                    $c->iso3,
                    $c->region,
                    $c->capital,
                    $c->population,
                    $gdp ? number_format((float) $gdp, 0, '.', '') : '0',
                    $c->riskScore ? number_format((float) $c->riskScore->risk_score, 2, '.', '') : '0.00',
                    $c->ports->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
