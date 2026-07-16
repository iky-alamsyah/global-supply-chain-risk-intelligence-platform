<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\RiskScoreDTO;
use App\Models\CountryRiskScore;

class RiskScoreRepository
{
    public function updateOrCreate(
        RiskScoreDTO $dto
    ): CountryRiskScore {

        return CountryRiskScore::updateOrCreate(

            [
                'country_id' => $dto->countryId,
            ],

            $dto->toArray()

        );
    }
}