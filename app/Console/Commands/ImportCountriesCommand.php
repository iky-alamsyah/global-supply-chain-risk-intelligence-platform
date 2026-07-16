<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CountryImportService;

class ImportCountriesCommand extends Command
{
    /**
     * Command Signature
     */
    protected $signature = 'countries:import';

    /**
     * Command Description
     */
    protected $description = 'Import countries from REST Countries API';

    public function __construct(
        protected CountryImportService $countryImportService
    ) {
        parent::__construct();
    }

    /**
     * Execute the command.
     */
    public function handle(): int
    {
        $this->newLine();

        $this->info('==============================================');
        $this->info(' Global Supply Chain Intelligence Platform');
        $this->info(' Country Import');
        $this->info('==============================================');

        $this->newLine();

        $this->info('Connecting to REST Countries API...');

        try {

            $total = $this->countryImportService->import();

            $this->newLine();

            $this->info('==============================================');
            $this->info("Successfully Imported : {$total} Countries");
            $this->info('==============================================');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->newLine();

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}