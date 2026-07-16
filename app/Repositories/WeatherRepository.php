<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\WeatherDTO;
use App\Models\WeatherCache;

class WeatherRepository
{
    public function updateOrCreate(WeatherDTO $dto): WeatherCache
    {
        return WeatherCache::updateOrCreate(
            [
                'country_id' => $dto->countryId,
            ],
            $dto->toArray()
        );
    }
}