<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\WorldBankDTO;
use App\Models\Country;

class WorldBankMapper
{
    /**
     * Convert World Bank responses to DTO.
     */
    public static function fromApi(
        Country $country,
        array $gdp,
        array $inflation,
        array $population,
        array $export,
        array $import
    ): WorldBankDTO {
        $gdpItem = self::findLatestNonNull($gdp);
        $inflationItem = self::findLatestNonNull($inflation);
        $populationItem = self::findLatestNonNull($population);
        $exportItem = self::findLatestNonNull($export);
        $importItem = self::findLatestNonNull($import);

        return new WorldBankDTO(
            countryId: $country->id,

            gdp: $gdpItem ? (float) $gdpItem['value'] : null,

            inflation: $inflationItem ? (float) $inflationItem['value'] : null,

            population: $populationItem ? (int) $populationItem['value'] : null,

            export: $exportItem ? (float) $exportItem['value'] : null,

            import: $importItem ? (float) $importItem['value'] : null,

            year: $gdpItem ? (int) $gdpItem['date'] : now()->year - 1,
        );
    }

    /**
     * Scan the response list and return the first item with a non-null value.
     */
    private static function findLatestNonNull(?array $response): ?array
    {
        if (empty($response) || !isset($response[1]) || !is_array($response[1])) {
            return null;
        }

        foreach ($response[1] as $item) {
            if (isset($item['value']) && $item['value'] !== null) {
                return $item;
            }
        }

        return null;
    }
}