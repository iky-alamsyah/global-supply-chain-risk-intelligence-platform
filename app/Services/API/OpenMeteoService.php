<?php

declare(strict_types=1);

namespace App\Services\API;

class OpenMeteoService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = config('services.open_meteo.base_url');
        $this->apiKey = null;
    }

    /**
     * Get current weather.
     */
    public function current(
        float $latitude,
        float $longitude
    ): array {

        $response = $this->get('/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => implode(',', [
                'temperature_2m',
                'rain',
                'wind_speed_10m',
                'weather_code',
                'relative_humidity_2m',
                'surface_pressure',
                'cloud_cover',
            ]),
            'timezone' => 'auto',
        ]);

        if (! $this->success($response)) {
            return [];
        }

        return $response->json();
    }
}