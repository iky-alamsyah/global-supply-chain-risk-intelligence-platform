<?php

declare(strict_types=1);

namespace App\Services\API;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseApiService
{
    protected string $baseUrl;

    protected ?string $apiKey = null;

    /**
     * Default HTTP Client
     */
    public function client(): PendingRequest
{
    $client = Http::acceptJson()
        ->retry(3, 1000)
        ->connectTimeout(10)
        ->timeout(20);

    if (!empty($this->apiKey)) {
        $client = $client->withToken($this->apiKey);
    }

    return $client;
}

    /**
     * GET Request
     */
    protected function get(
        string $endpoint,
        array $query = []
    ): Response {

        return $this->client()
            ->get(
                $this->baseUrl . $endpoint,
                $query
            );
    }

    /**
     * POST Request
     */
    protected function post(
        string $endpoint,
        array $data = []
    ): Response {

        return $this->client()
            ->post(
                $this->baseUrl . $endpoint,
                $data
            );
    }

    /**
     * Check Response
     */
    protected function success(Response $response): bool
    {
        return $response->successful();
    }
}