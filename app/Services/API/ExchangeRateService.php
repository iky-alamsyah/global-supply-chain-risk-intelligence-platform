<?php

declare(strict_types=1);

namespace App\Services\API;

class ExchangeRateService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = config('services.exchange_rate.base_url');
        $this->apiKey = null;
    }

    /**
     * Get latest exchange rates.
     */
    public function latest(string $baseCurrency = 'USD'): array
    {
        $response = $this->get(
            "/latest/{$baseCurrency}"
        );

        if (! $this->success($response)) {
            return [];
        }

        return $response->json();
    }
}