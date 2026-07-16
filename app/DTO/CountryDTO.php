<?php

declare(strict_types=1);

namespace App\DTO;

class CountryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $officialName,
        public readonly string $iso2,
        public readonly string $iso3,
        public readonly ?string $numericCode,
        public readonly ?string $region,
        public readonly ?string $subregion,
        public readonly ?string $capital,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $currencyCode,
        public readonly ?string $currencyName,
        public readonly ?string $currencySymbol,
        public readonly ?string $flag,
        public readonly ?string $flagPng,
        public readonly ?string $flagSvg,
        public readonly array $languages,
        public readonly ?int $population,
        public readonly bool $isActive = true,
    ) {
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'official_name' => $this->officialName,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'numeric_code' => $this->numericCode,
            'region' => $this->region,
            'subregion' => $this->subregion,
            'capital' => $this->capital,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'currency_code' => $this->currencyCode,
            'currency_name' => $this->currencyName,
            'currency_symbol' => $this->currencySymbol,
            'flag' => $this->flag,
            'flag_png' => $this->flagPng,
            'flag_svg' => $this->flagSvg,
            'languages' => $this->languages,
            'population' => $this->population,
            'is_active' => $this->isActive,
        ];
    }
}