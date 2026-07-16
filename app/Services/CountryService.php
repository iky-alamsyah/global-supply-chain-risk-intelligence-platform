<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryService
{
    public function getCountries(Request $request)
    {
        $query = Country::with([
            'riskScore',
            'weatherCaches',
            'currencyCaches'
        ]);

        // Search Country
        if ($request->filled('search')) {

            $query->where('name', 'like', '%' . $request->search . '%');

        }

        // Filter Region
        if ($request->filled('region')) {

            $query->where('region', $request->region);

        }

        // Filter Risk
        if ($request->filled('risk')) {

            $query->whereHas('riskScore', function ($q) use ($request) {

                $q->where('risk_level', $request->risk);

            });

        }

        return $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
    }
}