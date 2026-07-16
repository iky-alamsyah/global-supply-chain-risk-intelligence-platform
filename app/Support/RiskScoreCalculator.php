<?php

declare(strict_types=1);

namespace App\Support;

class RiskScoreCalculator
{
    public static function calculate(
        float $gdp,
        float $inflation,
        float $weather,
        float $currency,
        float $news
    ): float {

        return round(

            ($gdp * 0.25) +

            ($inflation * 0.20) +

            ($weather * 0.20) +

            ($currency * 0.15) +

            ($news * 0.20),

            2

        );
    }

    public static function level(
        float $score
    ): string {

        return match (true) {

            $score >= 70 => 'HIGH',

            $score >= 40 => 'MEDIUM',

            default => 'LOW',

        };
    }
}