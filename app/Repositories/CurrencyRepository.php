<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\CurrencyDTO;
use App\Models\CurrencyCache;

class CurrencyRepository
{
    /**
     * Create or update currency cache.
     */
    public function updateOrCreate(CurrencyDTO $dto): CurrencyCache
    {
        $oldCache = CurrencyCache::where('country_id', $dto->countryId)->first();
        $oldRate = $oldCache ? $oldCache->exchange_rate : null;
        $newRate = $dto->exchangeRate;

        $cache = CurrencyCache::updateOrCreate(
            [
                'country_id' => $dto->countryId,
            ],
            $dto->toArray()
        );

        // If rate is new or has changed, record in history
        if ($oldRate === null || (float)$oldRate !== (float)$newRate) {
            \App\Models\CurrencyHistory::create([
                'country_id' => $dto->countryId,
                'base_currency' => $dto->baseCurrency,
                'target_currency' => $dto->targetCurrency,
                'old_rate' => $oldRate,
                'new_rate' => $newRate,
                'change_percentage' => $dto->changePercentage,
            ]);
        }

        return $cache;
    }
}