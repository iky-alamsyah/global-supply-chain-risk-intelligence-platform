<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\CurrencyDTO;
use App\Models\Country;

class CurrencyMapper
{
    /**
     * Convert Exchange Rate API response to CurrencyDTO.
     */
    public static function fromApi(
        Country $country,
        array $response
    ): ?CurrencyDTO {

        $currency = $country->currency_code;

        if (
            empty($currency) ||
            !isset($response['rates'][$currency])
        ) {
            return null;
        }

        $rate = (float) $response['rates'][$currency];

        // Retrieve last cache to calculate change percentage
        $lastCache = \App\Models\CurrencyCache::where('country_id', $country->id)
            ->latest('rate_time')
            ->first();

        $changePercentage = null;
        $previousExchangeRate = null;
        if ($lastCache && $lastCache->exchange_rate > 0) {
            $previousExchangeRate = (float) $lastCache->exchange_rate;
            $changePercentage = (($rate - $previousExchangeRate) / $previousExchangeRate) * 100;
        }

        // Calculate currency risk score
        $currencyRiskScore = self::calculateCurrencyRiskScore($country, $changePercentage ?? 0.0);

        return new CurrencyDTO(
            countryId: $country->id,
            baseCurrency: $response['base_code'] ?? 'USD',
            targetCurrency: $currency,
            exchangeRate: $rate,
            lastUpdated: $response['time_last_update_utc'] ?? now()->toDateTimeString(),
            changePercentage: $changePercentage,
            currencyRiskScore: $currencyRiskScore,
            previousExchangeRate: $previousExchangeRate
        );
    }

    /**
     * Calculate Currency Risk Score using:
     * 1. Volatilitas Exchange Rate (50%)
     * 2. Inflasi Negara (20%)
     * 3. Sentimen Berita Ekonomi Negatif (20%)
     * 4. Risk Score Negara (10%)
     */
    public static function calculateCurrencyRiskScore(Country $country, float $changePercentage): float
    {
        $components = [];
        $weights = [];

        // 1. Volatility Score (50%)
        // Absolute change percentage scaled. E.g. 5% change is maximum risk (100)
        $components['volatility'] = min(100.0, abs($changePercentage) * 20.0);
        $weights['volatility'] = 0.50;

        // 2. Inflation Score (20%)
        $latestStat = $country->statistics()->orderByDesc('year')->first();
        if ($latestStat && $latestStat->inflation !== null) {
            $inflation = (float) $latestStat->inflation;
            // Scale: inflation <= 2% is 0 risk, >= 15% is 100 risk
            $components['inflation'] = min(100.0, max(0.0, ($inflation - 2.0) * (100 / 13.0)));
            $weights['inflation'] = 0.20;
        }

        // 3. News Sentiment Score (20%)
        $newsCount = $country->newsCaches()->count();
        if ($newsCount > 0) {
            $negativeNews = $country->newsCaches()->where('sentiment', 'negative')->count();
            $components['sentiment'] = ($negativeNews / $newsCount) * 100.0;
            $weights['sentiment'] = 0.20;
        }

        // 4. Country Risk Score (10%)
        $countryRisk = $country->riskScore;
        if ($countryRisk && $countryRisk->risk_score !== null) {
            $components['country'] = (float) $countryRisk->risk_score;
            $weights['country'] = 0.10;
        }

        // Proportional distribution of weights if any component is missing
        $totalWeight = array_sum($weights);
        if ($totalWeight === 0.0) {
            return 0.0;
        }

        $weightedSum = 0.0;
        foreach ($components as $key => $score) {
            $weightedSum += $score * ($weights[$key] / $totalWeight);
        }

        return min(100.0, max(0.0, $weightedSum));
    }
}