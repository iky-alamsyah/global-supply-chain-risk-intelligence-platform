<?php

declare(strict_types=1);

namespace App\DTO;

class WorldBankDTO
{
    public function __construct(
        public readonly int $countryId,
        public readonly ?float $gdp,
        public readonly ?float $inflation,
        public readonly ?int $population,
        public readonly ?float $export,
        public readonly ?float $import,
        public readonly int $year,
    ) {
    }

    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'gdp' => $this->gdp,
            'inflation' => $this->inflation,
            'population' => $this->population,
            'export' => $this->export,
            'import' => $this->import,
            'year' => $this->year,
        ];
    }
}