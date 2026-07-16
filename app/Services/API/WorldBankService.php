<?php

declare(strict_types=1);

namespace App\Services\API;

use App\Enums\WorldBankIndicator;

class WorldBankService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = config('services.world_bank.base_url');
        $this->apiKey = null;
    }

    /**
     * Generic indicator request.
     */
    protected function indicator(
        string $iso3,
        WorldBankIndicator $indicator,
        ?int $year = null
    ): array {

        $dateParam = $year ?: '2018:2025';

        $response = $this->get(
            "/country/{$iso3}/indicator/{$indicator->value}",
            [
                'format' => 'json',
                'date' => $dateParam,
            ]
        );

        if (! $this->success($response)) {
            return [];
        }

        return $response->json();
    }

    /**
     * GDP
     */
    public function getGDP(string $iso3, ?int $year = null): array
    {
        return $this->indicator(
            $iso3,
            WorldBankIndicator::GDP,
            $year
        );
    }

    /**
     * Inflation
     */
    public function getInflation(string $iso3, ?int $year = null): array
    {
        return $this->indicator(
            $iso3,
            WorldBankIndicator::INFLATION,
            $year
        );
    }

    /**
     * Population
     */
    public function getPopulation(string $iso3, ?int $year = null): array
    {
        return $this->indicator(
            $iso3,
            WorldBankIndicator::POPULATION,
            $year
        );
    }

    /**
     * Export
     */
    public function getExport(string $iso3, ?int $year = null): array
    {
        return $this->indicator(
            $iso3,
            WorldBankIndicator::EXPORT,
            $year
        );
    }

    /**
     * Import
     */
    public function getImport(string $iso3, ?int $year = null): array
    {
        return $this->indicator(
            $iso3,
            WorldBankIndicator::IMPORT,
            $year
        );
    }
}