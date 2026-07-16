<?php

declare(strict_types=1);

namespace App\Services\API;

class NewsDataService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = config('services.newsdata.base_url');
    }

    public function search(
    string $keyword,
    string $language = 'en',
    int $size = 10
): array {

    $response = $this->get('/latest', [

        'apikey' => config('services.newsdata.api_key'),

        'q' => $keyword,

        'language' => $language,

        'category' => 'business,politics,technology',

        'size' => $size,

    ]);

    if (! $this->success($response)) {
        return [];
    }

    return $response->json();
}
}