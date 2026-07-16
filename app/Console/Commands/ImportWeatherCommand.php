<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherImportService;

class ImportWeatherCommand extends Command
{
    protected $signature = 'weather:import';

    protected $description = 'Import weather data from Open-Meteo API';

    public function __construct(
        protected WeatherImportService $weatherImportService
    ) {
        parent::__construct();
    }

    public function handle(): int
{
    $this->newLine();

    $this->info('========================================');
    $this->info(' Open Meteo Weather Import');
    $this->info('========================================');

    $result = $this->weatherImportService->import();

    $this->newLine();

    $this->info("Success : {$result['success']}");

    $this->warn("Failed  : {$result['failed']}");

    return self::SUCCESS;
}
}