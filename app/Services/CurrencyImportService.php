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

    public function import(?\Closure $logCallback = null): array
    {
        $success = 0;
        $failed = 0;
        $skipped = 0;
        $details = [];

        if ($logCallback) {
            $logCallback('info', 'Connecting to Exchange Rate API...');
        }

        try {
            $response = $this->exchangeRateService->latest();
            if (empty($response['rates'])) {
                throw new \Exception('Exchange rate API response does not contain rates.');
            }

            $baseCode = $response['base_code'] ?? 'USD';
            if ($logCallback) {
                $logCallback('info', "HTTP 200 - Exchange rates retrieved successfully (Base: {$baseCode}).");
            }

            $countries = $this->countryRepository->activeCountries();

            foreach ($countries as $country) {
                $currency = $country->currency_code;
                if (empty($currency)) {
                    $skipped++;
                    $details[$country->name] = 'Skipped: Currency code not configured';
                    if ($logCallback) {
                        $logCallback('warn', "Country [{$country->name}]: Skipped - Currency code is empty.");
                    }
                    continue;
                }

                if (!isset($response['rates'][$currency])) {
                    try {
                        $this->useLastCacheOrThrow($country, "Currency {$currency} not supported by API");
                        $success++;
                        $details[$country->name] = "Success: Fell back to last cache (Currency {$currency} not supported by API)";
                        if ($logCallback) {
                            $logCallback('warn', "Country [{$country->name}]: Currency {$currency} not supported by API, fell back to last cache.");
                        }
                    } catch (\Throwable $cacheEx) {
                        $skipped++;
                        $details[$country->name] = "Skipped: Currency {$currency} not supported by API and no cache available";
                        if ($logCallback) {
                            $logCallback('warn', "Country [{$country->name}]: Skipped - Currency {$currency} not supported by API and no cache available.");
                        }
                    }
                    continue;
                }

                try {
                    $dto = CurrencyMapper::fromApi($country, $response);
                    if (!$dto) {
                        throw new \Exception("Exchange rate not available in response for currency: {$currency}");
                    }

                    $this->currencyRepository->updateOrCreate($dto);
                    $success++;
                    $details[$country->name] = "Success: Updated from API (Rate: {$dto->exchangeRate})";
                    if ($logCallback) {
                        $logCallback('info', "Country [{$country->name}]: Exchange rate updated successfully ({$baseCode} to {$currency}: {$dto->exchangeRate}).");
                    }
                } catch (\Throwable $e) {
                    try {
                        $this->useLastCacheOrThrow($country, $e->getMessage());
                        $success++;
                        $details[$country->name] = 'Success: Fell back to last cache (' . $e->getMessage() . ')';
                        if ($logCallback) {
                            $logCallback('warn', "Country [{$country->name}]: API mapping failed, fell back to last cache. Error: " . $e->getMessage());
                        }
                    } catch (\Throwable $cacheEx) {
                        $failed++;
                        $details[$country->name] = 'Failed: ' . $e->getMessage();
                        if ($logCallback) {
                            $logCallback('error', "Country [{$country->name}]: Import failed and no cache available. Error: " . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            if ($logCallback) {
                $logCallback('error', "CRITICAL: Global Exchange Rate Import failed completely: " . $e->getMessage() . ". Attempting cache fallback for all countries...");
            }

            // Attempt cache fallback for all active countries
            $countries = $this->countryRepository->activeCountries();
            foreach ($countries as $country) {
                try {
                    $this->useLastCacheOrThrow($country, 'Global API request failed: ' . $e->getMessage());
                    $success++;
                    $details[$country->name] = 'Success: Fell back to last cache (Global API failure)';
                    if ($logCallback) {
                        $logCallback('warn', "Country [{$country->name}]: Fell back to last cache due to global API failure.");
                    }
                } catch (\Throwable $ex) {
                    $failed++;
                    $details[$country->name] = 'Failed: ' . $ex->getMessage();
                    if ($logCallback) {
                        $logCallback('error', "Country [{$country->name}]: Cache fallback failed: " . $ex->getMessage());
                    }
                }
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'skipped' => $skipped,
            'details' => $details,
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