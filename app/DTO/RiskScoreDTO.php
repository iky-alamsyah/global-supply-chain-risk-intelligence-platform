<?php

declare(strict_types=1);

namespace App\DTO;

class RiskScoreDTO
{
    public function __construct(
        public readonly int $countryId,
        public readonly float $gdpScore,
        public readonly float $inflationScore,
        public readonly float $weatherScore,
        public readonly float $currencyScore,
        public readonly float $newsScore,
        public readonly float $riskScore,
        public readonly string $riskLevel,
    ) {
    }

    public function toArray(): array
    {
        return [
            'country_id'       => $this->countryId,
            'gdp_score'        => $this->gdpScore,
            'inflation_score'  => $this->inflationScore,
            'weather_score'    => $this->weatherScore,
            'currency_score'   => $this->currencyScore,
            'news_score'       => $this->newsScore,
            'risk_score'       => $this->riskScore,
            'risk_level'       => $this->riskLevel,
        ];
    }
}