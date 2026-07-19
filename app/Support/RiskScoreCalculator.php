<?php

declare(strict_types=1);

namespace App\Support;

class RiskScoreCalculator
{
    /**
     * Calculate Country Risk Score (0-100) using a balanced indicator distribution:
     * - GDP Risk (Economy Size): 20%
     * - Inflation Risk (Macro Stability): 15%
     * - Weather Risk (Climate Disruption): 20%
     * - Currency Risk (Exchange Rate Volatility): 15%
     * - News Risk (International Sentiment): 15%
     * - Ports Risk (Maritime Logistics Infrastructure): 15%
     */
    public static function calculate(
        float $gdp,
        float $inflation,
        float $weather,
        float $currency,
        float $news,
        float $ports
    ): float {
        return round(
            ($gdp * 0.20) +
            ($inflation * 0.15) +
            ($weather * 0.20) +
            ($currency * 0.15) +
            ($news * 0.15) +
            ($ports * 0.15),
            2
        );
    }

    /**
     * Map Country Risk Score to a realistic Risk Level threshold.
     * Evaluated and calibrated using simulation:
     * - Score < 42.0       => LOW
     * - Score 42.0 - 51.9  => MEDIUM
     * - Score >= 52.0      => HIGH
     */
    public static function level(
        float $score
    ): string {
        return match (true) {
            $score >= 52.0 => 'HIGH',
            $score >= 42.0 => 'MEDIUM',
            default => 'LOW',
        };
    }
}