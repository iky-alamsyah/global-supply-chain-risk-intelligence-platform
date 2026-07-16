<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshWeatherJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly \App\Models\Country $country,
        public readonly bool $force = false
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        \App\Services\WeatherImportService $weatherImportService,
        \App\Services\RiskEngineService $riskEngineService
    ): void {
        try {
            $weatherImportService->importForCountry($this->country, $this->force);
            $riskEngineService->save($this->country);
            
            \Illuminate\Support\Facades\Log::info("RefreshWeatherJob completed for country: {$this->country->name}");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("RefreshWeatherJob failed for country {$this->country->name}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
