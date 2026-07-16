<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCountryStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-country-statistics {--country= : ISO3 code of specific country to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import GDP and macroeconomic statistics from World Bank API';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\CountryStatisticsImportService $statisticsImportService): int
    {
        $this->newLine();
        $this->info('==================================================');
        $this->info(' World Bank Country Statistics Import');
        $this->info('==================================================');

        $countryIso3 = $this->option('country');
        
        if ($countryIso3) {
            $country = \App\Models\Country::where('iso3', strtoupper($countryIso3))->first();
            if (!$country) {
                $this->error("Country with ISO3 code '{$countryIso3}' not found.");
                return self::FAILURE;
            }
            
            $this->info("Importing statistics for {$country->name} ({$country->iso3})...");
            
            try {
                // Call import for single country
                $gdp = $statisticsImportService->importForSingleCountry($country);
                $this->info("Successfully imported statistics for {$country->name}.");
                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error("Failed to import statistics for {$country->name}: " . $e->getMessage());
                return self::FAILURE;
            }
        }

        $this->info("Starting batch import for all active countries...");
        $result = $statisticsImportService->import();
        $this->newLine();
        $this->info("Imported statistics for {$result} countries.");

        return self::SUCCESS;
    }
}
