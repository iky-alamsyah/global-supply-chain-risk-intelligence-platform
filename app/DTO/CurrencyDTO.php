<?php

declare(strict_types=1);

namespace App\DTO;

class CurrencyDTO
{
    public function __construct(
        public readonly int $countryId,
        public readonly string $baseCurrency,
        public readonly string $targetCurrency,
        public readonly float $exchangeRate,
        public readonly string $lastUpdated,
        public readonly float $changePercentage = 0.0,
        public readonly float $currencyRiskScore = 0.0,
        public readonly ?float $previousExchangeRate = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'base_currency' => $this->baseCurrency,
            'target_currency' => $this->targetCurrency,
            'exchange_rate' => $this->exchangeRate,
            'previous_exchange_rate' => $this->previousExchangeRate,
            'change_percentage' => $this->changePercentage,
            'currency_risk_score' => $this->currencyRiskScore,
            'rate_time' => $this->lastUpdated ? \Carbon\Carbon::parse($this->lastUpdated)->toDateTimeString() : now()->toDateTimeString(),
            'expires_at' => now()->addHours(24)->toDateTimeString(),
        ];
    }
}