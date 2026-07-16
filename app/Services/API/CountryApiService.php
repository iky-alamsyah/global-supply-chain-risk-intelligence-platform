<?php

declare(strict_types=1);

namespace App\Services\API;

class CountryApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = config('services.country_api.base_url');

        $this->apiKey = config('services.country_api.api_key');
    }

    /**
     * Search Country
     */
    public function search(string $keyword): array
    {
        $response = $this->get('', [
            'q' => $keyword,
        ]);

        if (! $this->success($response)) {
            return [];
        }

        return $response->json();
    }

    /**
 * Get paginated countries.
 */
public function getCountries(
    int $limit = 100,
    int $offset = 0
): array {

    $response = $this->get('', [
        'limit' => $limit,
        'offset' => $offset,
    ]);

    if (! $this->success($response)) {
        return [];
    }

    return $response->json();
}
    /**
     * Get Country by ISO Code
     */
    public function byCode(string $code): array
    {
        $response = $this->get('', [
            'code' => $code,
        ]);

        if (! $this->success($response)) {
            return [];
        }

        return $response->json();
    }

    /**
     * Check API Connection
     */
    public function ping(): bool
    {
        $response = $this->get('', [
            'q' => 'indonesia',
        ]);

        return $this->success($response);
    }
}