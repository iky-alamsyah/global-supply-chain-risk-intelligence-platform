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

        // Calculate economy size risk (with population-based fallback)
        $gdpScore = $this->calculateGdpRisk(
            $country,
            $statistics?->gdp
        );

        // Calculate macro stability risk (with geographic subregion-based fallback)
        $inflationScore = $this->calculateInflationRisk(
            $country,
            $statistics?->inflation
        );

        // Weather risk fallback: 35.0 (standard risk baseline if no weather cache exists)
        $weatherScore = $weather?->weather_risk_score ?? 35.0;

        // Currency risk fallback: 40.0
        $currencyScore = $currency?->currency_risk_score ?? 40.0;

        // News sentiment risk fallback: 50.0
        $newsScore = $news?->news_risk_score ?? 50.0;

        // Ports logistics infrastructure risk
        $portsScore = $this->calculatePortsRisk($country);

        $riskScore = RiskScoreCalculator::calculate(
            $gdpScore,
            $inflationScore,
            $weatherScore,
            $currencyScore,
            $newsScore,
            $portsScore
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
     * Calculate GDP Risk
     * Fallback to deterministic population-based sizing if statistics are null
     */
    private function calculateGdpRisk(Country $country, mixed $gdp): float
    {
        if ($gdp !== null && $gdp !== '' && $gdp !== 'N/A' && $gdp !== '-') {
            $gdpFloat = (float) $gdp;
            if ($gdpFloat > 0.0) {
                if ($gdpFloat >= 1000000000000) {
                    return 10.0;
                }
                if ($gdpFloat >= 100000000000) {
                    return 35.0;
                }
                if ($gdpFloat >= 1000000000) {
                    return 65.0;
                }
                return 85.0;
            }
        }

        // Fallback using pre-populated country population
        $pop = (int) $country->population;
        if ($pop > 50000000) {
            return 25.0; // Likely a larger economy
        } elseif ($pop > 10000000) {
            return 45.0;
        } elseif ($pop > 1000000) {
            return 65.0;
        } else {
            return 85.0; // Likely a very small economy
        }
    }

    /**
     * Calculate Inflation Risk
     * Fallback to geographic subregion baseline if statistics are null
     */
    private function calculateInflationRisk(Country $country, mixed $inflation): float
    {
        if ($inflation !== null && $inflation !== '' && $inflation !== 'N/A' && $inflation !== '-') {
            $inflationFloat = (float) $inflation;
            if ($inflationFloat === 0.0 && $inflation !== 0 && $inflation !== '0' && $inflation !== 0.0) {
                // Keep processing if not a string error
            } else {
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

        // Geographic subregion / region based baseline fallback
        $subregion = $country->subregion;
        $lowRiskSubregions = [
            'Western Europe', 'Northern Europe', 'Southern Europe',
            'Northern America', 'Eastern Asia', 'Australia and New Zealand'
        ];
        $highRiskSubregions = [
            'South America', 'Central America', 'Caribbean',
            'Western Asia', 'Eastern Europe', 'Northern Africa',
            'Middle Africa', 'Eastern Africa', 'Western Africa', 'Southern Africa'
        ];

        if ($subregion && in_array($subregion, $lowRiskSubregions)) {
            return 20.0;
        }
        if ($subregion && in_array($subregion, $highRiskSubregions)) {
            return 65.0;
        }

        return 45.0;
    }

    /**
     * Calculate Ports logistics infrastructure risk
     * Landlocked countries without sea access get a higher supply chain baseline risk (55.0).
     * Countries with ports get a score proportional to their inactive-to-total-ports ratio.
     */
    private function calculatePortsRisk(Country $country): float
    {
        $totalPorts = $country->ports()->count();
        if ($totalPorts > 0) {
            $inactivePorts = $country->ports()->whereRaw("upper(trim(status)) != 'ACTIVE'")->count();
            return ($inactivePorts / $totalPorts) * 100.0;
        }

        // Landlocked countries get higher baseline logistics risk
        return 55.0;
    }
}