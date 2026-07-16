<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsImportService;

class ImportNewsCommand extends Command
{
    /**
     * Command signature.
     */
    protected $signature = 'news:import';

    /**
     * Command description.
     */
    protected $description = 'Import latest news from NewsData.io';

    public function __construct(
        protected NewsImportService $newsImportService
    ) {
        parent::__construct();
    }

    /**
     * Execute the command.
     */
    public function handle(): int
    {
        $this->newLine();

        $this->info('======================================');
        $this->info(' NewsData Import');
        $this->info('======================================');

        $result = $this->newsImportService->import();

        $this->newLine();

        $this->info("Success : {$result['success']}");
        $this->warn("Failed  : {$result['failed']}");

        $this->newLine();

        return self::SUCCESS;
    }
}