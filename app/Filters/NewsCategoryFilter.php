<?php

declare(strict_types=1);

namespace App\Filters;

class NewsCategoryFilter
{
    protected const ALLOWED = [

        'business',

        'world',

        'politics',

        'technology',

        'environment',

    ];

    public static function allowed(array $categories): bool
    {
        return ! empty(
            array_intersect(
                self::ALLOWED,
                $categories
            )
        );
    }
}