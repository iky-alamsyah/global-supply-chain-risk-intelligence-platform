<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\CountryDTO;

class CountryMapper
{
    /**
     * Convert REST Countries API response to CountryDTO.
     */
    public static function fromApi(array $country): CountryDTO
    {
        // Currency
        $currency = $country['currencies'][0] ?? [];

        // Capital
        $capital = $country['capitals'][0]['name'] ?? null;

        // Coordinates
        $latitude = $country['coordinates']['lat'] ?? null;
        $longitude = $country['coordinates']['lng'] ?? null;

        // Flag
        $flag = $country['flag']['emoji'] ?? null;
        $flagPng = $country['flag']['url_png'] ?? null;
        $flagSvg = $country['flag']['url_svg'] ?? null;

        return new CountryDTO(
            name: $country['names']['common'] ?? '',
            officialName: $country['names']['official'] ?? null,
            iso2: $country['codes']['alpha_2'] ?? '',
            iso3: $country['codes']['alpha_3'] ?? '',
            numericCode: $country['codes']['ccn3'] ?? null,
            region: $country['region'] ?? null,
            subregion: $country['subregion'] ?? null,
            capital: $capital,
            latitude: $latitude,
            longitude: $longitude,
            currencyCode: $currency['code'] ?? null,
            currencyName: $currency['name'] ?? null,
            currencySymbol: $currency['symbol'] ?? null,
            flag: $flag,
            flagPng: $flagPng,
            flagSvg: $flagSvg,
            languages: $country['languages'] ?? [],
            population: $country['population'] ?? null,
            isActive: true
        );
    }
}