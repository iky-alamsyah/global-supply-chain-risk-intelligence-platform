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
    private function calculateGdpRisk(?float $gdp): float
    {
        if ($gdp === null) {
            return 50;
        }

        if ($gdp >= 1000000000000) {
            return 10;
        }

        if ($gdp >= 100000000000) {
            return 30;
        }

        if ($gdp >= 10000000000) {
            return 60;
        }

        return 90;
    }

    /**
     * Inflation Risk
     */
    private function calculateInflationRisk(?float $inflation): float
    {
        if ($inflation === null) {
            return 50;
        }

        if ($inflation <= 2) {
            return 10;
        }

        if ($inflation <= 5) {
            return 30;
        }

        if ($inflation <= 10) {
            return 60;
        }

        return 90;
    }
}