<?php

declare(strict_types=1);

namespace App\Support;

class NewsKeywordBuilder
{
    /**
     * Economic related keywords.
     */
    protected const ECONOMY = [
        'trade',
        'export',
        'import',
        'economy',
        'inflation',
        'gdp',
        'economic growth',
    ];

    /**
     * Logistics & Supply Chain.
     */
    protected const LOGISTICS = [
        'logistics',
        'shipping',
        'port',
        'freight',
        'container',
        'supply chain',
        'cargo',
    ];

    /**
     * Politics.
     */
    protected const POLITICS = [
        'tariff',
        'sanction',
        'embargo',
        'trade war',
        'government policy',
        'customs',
    ];

    /**
     * Disaster.
     */
    protected const DISASTER = [
        'earthquake',
        'flood',
        'storm',
        'wildfire',
        'hurricane',
        'drought',
        'tsunami',
    ];

    /**
     * Build query.
     */
    public static function build(string $country): array
{
    return [

        $country . ' logistics',

    ];
}

    /**
     * Get all keywords.
     */
    public static function keywords(): array
    {
        return array_merge(
            self::ECONOMY,
            self::LOGISTICS,
            self::POLITICS,
            self::DISASTER,
        );
    }
}