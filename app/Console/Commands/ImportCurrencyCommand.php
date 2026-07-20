<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyImportService;

class ImportCurrencyCommand extends Command
{
    protected $signature = 'currency:import';

    protected $description = 'Import exchange rates';

    public function __construct(
        protected CurrencyImportService $currencyImportService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->newLine();

        $this->info('======================================');
        $this->info(' Exchange Rate Import');
        $this->info('======================================');

        $result = $this->currencyImportService->import(function (string $level, string $message) {
            if ($level === 'error') {
                $this->error($message);
            } elseif ($level === 'warn') {
                $this->warn($message);
            } else {
                $this->line($message);
            }
        });

        $this->newLine();

        $success = $result['success'] ?? 0;
        $failed = $result['failed'] ?? 0;
        $skipped = $result['skipped'] ?? 0;
        $total = $success + $failed + $skipped;

        $this->info("======================================");
        $this->line("Total Countries : {$total}");
        $this->info("Imported        : {$success}");
        $this->comment("Skipped         : {$skipped}");
        $this->warn("Failed          : {$failed}");
        $this->info("======================================");

        return self::SUCCESS;
    }
}