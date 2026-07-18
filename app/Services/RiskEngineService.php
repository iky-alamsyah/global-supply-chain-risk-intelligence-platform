<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\RiskScoreDTO;
use App\Models\Country;
use App\Repositories\RiskScoreRepository;
use App\Support\RiskScoreCalculator;

class RiskEngineService
{
    public function __construct(
        protected RiskScoreRepository $riskScoreRepository,
    ) {
    }

    /**
     * Calculate and save all country risk scores.
     */
    public function calculate(Country $country): RiskScoreDTO
    {
        $statistics = $country->statistics()
            ->latest('year')
            ->first();

        $weather = $country->weatherCaches()
            ->latest('weather_time')
            ->first();

        $currency = $country->currencyCaches()
            ->latest('rate_time')
            ->first();

        $news = $country->newsCaches()
            ->latest('published_at')
            ->first();

        $gdpScore = $this->calculateGdpRisk(
            $statistics?->gdp
        );

        $inflationScore = $this->calculateInflationRisk(
            $statistics?->inflation
        );

        $weatherScore = $weather?->weather_risk_score ?? 50;

        $currencyScore = $currency?->currency_risk_score ?? 50;

        $newsScore = $news?->news_risk_score ?? 50;

        $riskScore = RiskScoreCalculator::calculate(
            $gdpScore,
            $inflationScore,
            $weatherScore,
            $currencyScore,
            $newsScore
        );

        return new RiskScoreDTO(
            countryId: $country->id,
            gdpScore: $gdpScore,
            inflationScore: $inflationScore,
            weatherScore: $weatherScore,
            currencyScore: $currencyScore,
            newsScore: $newsScore,
            riskScore: $riskScore,
            riskLevel: RiskScoreCalculator::level($riskScore),
        );
    }

    /**
     * Save result.
     */
    public function save(Country $country): void
    {
        $dto = $this->calculate($country);

        $this->riskScoreRepository
            ->updateOrCreate($dto);
    }

    /**
     * GDP Risk
     */
    private function calculateGdpRisk(mixed $gdp): float
    {
        if ($gdp === null || $gdp === '' || $gdp === 'N/A' || $gdp === '-') {
            return 50.0;
        }

        $gdpFloat = (float) $gdp;
        if ($gdpFloat <= 0.0) {
            return 50.0;
        }

        if ($gdpFloat >= 1000000000000) {
            return 10.0;
        }

        if ($gdpFloat >= 100000000000) {
            return 30.0;
        }

        if ($gdpFloat >= 10000000000) {
            return 60.0;
        }

        return 90.0;
    }

    /**
     * Inflation Risk
     */
    private function calculateInflationRisk(mixed $inflation): float
    {
        if ($inflation === null || $inflation === '' || $inflation === 'N/A' || $inflation === '-') {
            return 50.0;
        }

        $inflationFloat = (float) $inflation;
        if ($inflationFloat === 0.0 && $inflation !== 0 && $inflation !== '0' && $inflation !== 0.0) {
            return 50.0;
        }

        if ($inflationFloat <= 2) {
            return 10.0;
        }

        if ($inflationFloat <= 5) {
            return 30.0;
        }

        if ($inflationFloat <= 10) {
            return 60.0;
        }

        return 90.0;
    }
}