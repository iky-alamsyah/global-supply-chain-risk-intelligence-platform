<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\WorldBankMapper;
use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Repositories\CountryStatisticsRepository;
use App\Services\API\WorldBankService;

class CountryStatisticsImportService
{
    public function __construct(
        protected WorldBankService $worldBank,
        protected CountryRepository $countryRepository,
        protected CountryStatisticsRepository $statisticsRepository,
    ) {
    }

    /**
     * Import World Bank statistics for all active countries.
     */
    public function import(): int
    {
        $total = 0;

        $countries = $this->countryRepository
            ->activeCountries();

        foreach ($countries as $country) {
            try {
                $this->importForSingleCountry($country);
                $total++;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to import statistics for {$country->name} ({$country->iso3}): " . $e->getMessage());
            }
        }

        return $total;
    }

    /**
     * Import statistics for a single country.
     */
    public function importForSingleCountry(Country $country): void
    {
        $gdp = $this->worldBank->getGDP($country->iso3);
        $inflation = $this->worldBank->getInflation($country->iso3);
        $population = $this->worldBank->getPopulation($country->iso3);
        $export = $this->worldBank->getExport($country->iso3);
        $import = $this->worldBank->getImport($country->iso3);

        $dto = WorldBankMapper::fromApi(
            $country,
            $gdp,
            $inflation,
            $population,
            $export,
            $import
        );

        $this->statisticsRepository->updateOrCreate($dto);
    }
}