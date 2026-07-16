<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\WorldBankDTO;
use App\Models\CountryStatistic;

class CountryStatisticsRepository
{
    public function updateOrCreate(WorldBankDTO $dto): CountryStatistic
    {
        return CountryStatistic::updateOrCreate(
            [
                'country_id' => $dto->countryId,
                'year' => $dto->year,
            ],
            $dto->toArray()
        );
    }
}