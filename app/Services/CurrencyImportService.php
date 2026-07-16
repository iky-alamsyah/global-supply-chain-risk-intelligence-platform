<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\CurrencyMapper;
use App\Repositories\CountryRepository;
use App\Repositories\CurrencyRepository;
use App\Services\API\ExchangeRateService;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class CurrencyImportService
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService,
        protected CountryRepository $countryRepository,
        protected CurrencyRepository $currencyRepository,
    ) {
    }

    /**
     * Import exchange rates for all countries.
     */
    public function import(): array
    {
        $success = 0;
        $failed = 0;

        try {
            $response = $this->exchangeRateService->latest();
            if (empty($response['rates'])) {
                throw new \Exception('Exchange rate API response does not contain rates.');
            }

            $countries = $this->countryRepository->activeCountries();

            foreach ($countries as $country) {
                try {
                    $dto = CurrencyMapper::fromApi($country, $response);
                    if (!$dto) {
                        throw new \Exception('Exchange rate not available in response for currency: ' . $country->currency_code);
                    }

                    $this->currencyRepository->updateOrCreate($dto);
                    $success++;
                } catch (\Throwable $e) {
                    Log::warning('Currency Import failed for country, attempting fallback to cache', [
                        'country' => $country->name,
                        'error' => $e->getMessage()
                    ]);

                    $this->useLastCacheOrThrow($country, $e->getMessage());
                    $failed++;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Global Exchange Rate Import failed completely', [
                'error' => $e->getMessage()
            ]);

            // Attempt cache fallback for all active countries
            $countries = $this->countryRepository->activeCountries();
            foreach ($countries as $country) {
                try {
                    $this->useLastCacheOrThrow($country, 'Global API request failed: ' . $e->getMessage());
                } catch (\Throwable $ex) {
                    $failed++;
                }
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * Import exchange rates for a single country.
     */
    public function importForCountry(Country $country): void
    {
        try {
            $response = $this->exchangeRateService->latest();
            if (empty($response['rates'])) {
                throw new \Exception('API response does not contain rates.');
            }

            $dto = CurrencyMapper::fromApi($country, $response);
            if (!$dto) {
                throw new \Exception('Exchange rate not available in API response for currency: ' . $country->currency_code);
            }

            $this->currencyRepository->updateOrCreate($dto);
            Log::info('Successfully updated currency rate for country', ['country' => $country->name]);
        } catch (\Throwable $e) {
            Log::warning('Currency API request failed for single country, attempting cache fallback', [
                'country' => $country->name,
                'error' => $e->getMessage()
            ]);

            $this->useLastCacheOrThrow($country, $e->getMessage());
        }
    }

    /**
     * Fallback to last cache and touch its updated_at timestamp.
     */
    protected function useLastCacheOrThrow(Country $country, string $reason): void
    {
        $lastCache = $country->currencyCaches()->orderByDesc('created_at')->first();
        if ($lastCache) {
            $lastCache->touch();
            Log::info('Fallback to last currency cache successful for country', [
                'country' => $country->name,
                'last_rate_time' => $lastCache->rate_time
            ]);
        } else {
            throw new \Exception('No currency cache available for fallback. Reason: ' . $reason);
        }
    }
}