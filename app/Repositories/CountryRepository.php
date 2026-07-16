<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\CountryDTO;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

class CountryRepository
{
    /**
     * Get all countries.
     */
    public function all(): Collection
    {
        return Country::orderBy('name')->get();
    }

    /**
     * Get active countries.
     */
    public function active(): Collection
    {
        return Country::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
 * Get all active countries.
 */
public function activeCountries()
{
    $countries = [

        'Indonesia',

        'Singapore',

        'Malaysia',

        'Thailand',

        'Vietnam',

        'China',

        'Japan',

        'South Korea',

        'India',

        'United States',

        'Germany',

        'Netherlands',

        'Australia',

        'United Kingdom',

        'United Arab Emirates',

    ];

    return Country::whereIn('name', $countries)
        ->orderBy('name')
        ->get();
}
    /**
     * Find by ISO2.
     */
    public function findByIso2(string $iso2): ?Country
    {
        return Country::where('iso2', $iso2)->first();
    }

    /**
     * Find by ISO3.
     */
    public function findByIso3(string $iso3): ?Country
    {
        return Country::where('iso3', $iso3)->first();
    }

    /**
     * Create or Update.
     */
    public function updateOrCreate(CountryDTO $dto): Country
    {
        return Country::updateOrCreate(
            [
                'iso3' => $dto->iso3,
            ],
            $dto->toArray()
        );
    }

    /**
     * Delete.
     */
    public function delete(Country $country): bool
    {
        return $country->delete();
    }
}