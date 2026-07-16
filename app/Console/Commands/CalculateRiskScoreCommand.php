<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\RiskEngineService;
use Illuminate\Console\Command;

class CalculateRiskScoreCommand extends Command
{
    protected $signature = 'risk:calculate';

    protected $description = 'Calculate country risk score';

    public function __construct(
        protected RiskEngineService $riskEngineService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $countries = Country::where('is_active', true)->get();

        $this->info("Calculating {$countries->count()} countries...");

        $bar = $this->output->createProgressBar(
            $countries->count()
        );

        $bar->start();

        foreach ($countries as $country) {

            $this->riskEngineService
                ->save($country);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);

        $this->info('Risk calculation completed.');

        return self::SUCCESS;
    }
}