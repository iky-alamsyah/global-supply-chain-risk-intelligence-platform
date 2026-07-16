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

        $result = $this->currencyImportService->import();

        $this->newLine();

        $this->info("Success : {$result['success']}");
        $this->warn("Failed  : {$result['failed']}");

        return self::SUCCESS;
    }
}