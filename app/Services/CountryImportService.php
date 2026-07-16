<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\CountryMapper;
use App\Repositories\CountryRepository;
use App\Services\API\CountryApiService;

class CountryImportService
{
    public function __construct(
        protected CountryApiService $countryApi,
        protected CountryRepository $countryRepository,
    ) {
    }

    /**
     * Import all countries from REST Countries API.
     */
    public function import(): int
    {
        $imported = 0;

        $offset = 0;
        $limit = 100;

        do {

            $response = $this->countryApi->getCountries(
                $limit,
                $offset
            );

            $countries = $response['data']['objects'] ?? [];

            foreach ($countries as $country) {

                $dto = CountryMapper::fromApi($country);

                $this->countryRepository
                    ->updateOrCreate($dto);

                $imported++;
            }

            $offset += $limit;

        } while (!empty($countries));

        return $imported;
    }
}