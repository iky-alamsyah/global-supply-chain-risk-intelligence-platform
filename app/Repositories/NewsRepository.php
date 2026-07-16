<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\NewsDTO;
use App\Models\NewsCache;

class NewsRepository
{
    public function updateOrCreate(
        NewsDTO $dto
    ): NewsCache {

        return NewsCache::updateOrCreate(

            [
                'url' => $dto->url,
            ],

            $dto->toArray()

        );
    }
}