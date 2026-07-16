<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\WeatherMapper;
use App\Repositories\CountryRepository;
use App\Repositories\WeatherRepository;
use App\Services\API\OpenMeteoService;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class WeatherImportService
{
    public function __construct(
        protected OpenMeteoService $openMeteo,
        protected CountryRepository $countryRepository,
        protected WeatherRepository $weatherRepository,
    ) {
    }

    /**
     * Import weather data for all active countries.
     */
    public function import(): array
    {
        $success = 0;
        $failed = 0;

        $countries = $this->countryRepository->activeCountries();

        foreach ($countries as $country) {
            try {
                $this->importForCountry($country);
                $success++;
            } catch (\Throwable $e) {
                Log::error('Weather Import Failed for Country', [
                    'country' => $country->name,
                    'iso3' => $country->iso3,
                    'message' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * Import weather data for a single country with coordinate fallback.
     */
    public function importForCountry(Country $country, bool $force = false): void
    {
        // Check if latest cache is still valid and force is false
        if (!$force) {
            $latestCache = $country->weatherCaches()->orderByDesc('created_at')->first();
            if ($latestCache && $latestCache->expires_at && \Carbon\Carbon::parse($latestCache->expires_at)->isFuture()) {
                Log::info('Weather cache is still valid for country, skipping API import', [
                    'country' => $country->name,
                    'expires_at' => \Carbon\Carbon::parse($latestCache->expires_at)->toDateTimeString(),
                ]);
                return;
            }
        }

        $coordinates = $this->getCountryCoordinates($country);

        if (!$coordinates) {
            // Log missing coordinates and try to use cache
            Log::warning('No coordinates found for country weather import', [
                'country' => $country->name
            ]);
            
            $this->useLastCacheOrThrow($country, 'No coordinates available');
            return;
        }

        try {
            $weather = $this->openMeteo->client()->timeout(15)->get(
                config('services.open_meteo.base_url') . '/forecast', [
                    'latitude' => $coordinates['lat'],
                    'longitude' => $coordinates['lng'],
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
                ]
            );

            if (!$weather->successful() || empty($weather->json())) {
                throw new \Exception('API responded with error code ' . $weather->status());
            }

            $weatherData = $weather->json();
            if (empty($weatherData['current'])) {
                throw new \Exception('Weather response does not contain current weather section');
            }

            // 1. Generate and save weather alerts based on rules
            $alerts = $this->generateWeatherAlerts($country, $weatherData['current']);

            // 2. Map and save weather cache
            $dto = WeatherMapper::fromApi($country, $weatherData, $alerts);
            $this->weatherRepository->updateOrCreate($dto);

            Log::info('Successfully imported weather for country', [
                'country' => $country->name,
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Weather API request failed, attempting fallback to last cache', [
                'country' => $country->name,
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
                'error' => $e->getMessage(),
            ]);

            $this->useLastCacheOrThrow($country, $e->getMessage());
        }
    }

    /**
     * Try to update the timestamp of the last cache to mark it as updated, or throw an exception if none exists.
     */
    protected function useLastCacheOrThrow(Country $country, string $reason): void
    {
        $lastCache = $country->weatherCaches()->orderByDesc('created_at')->first();
        if ($lastCache) {
            // Touch updated_at to show it was verified
            $lastCache->touch();
            Log::info('Fallback to last weather cache successful for country', [
                'country' => $country->name,
                'last_cache_time' => $lastCache->weather_time
            ]);
        } else {
            throw new \Exception('No weather cache available for country fallback. Reason: ' . $reason);
        }
    }

    /**
     * Get coordinates with fallback (country coordinates -> capital coordinates).
     */
    protected function getCountryCoordinates(Country $country): ?array
    {
        // 1. Try Country Coordinates
        if (!empty($country->latitude) && !empty($country->longitude)) {
            return [
                'lat' => (float) $country->latitude,
                'lng' => (float) $country->longitude,
            ];
        }

        // 2. Predefined Capital Coordinates as fallbacks for active countries
        $capitalCoords = [
            'Indonesia' => ['lat' => -6.2088, 'lng' => 106.8456],
            'Singapore' => ['lat' => 1.3521, 'lng' => 103.8198],
            'Malaysia' => ['lat' => 3.1390, 'lng' => 101.6869],
            'Thailand' => ['lat' => 13.7563, 'lng' => 100.5018],
            'Vietnam' => ['lat' => 21.0285, 'lng' => 105.8542],
            'China' => ['lat' => 39.9042, 'lng' => 116.4074],
            'Japan' => ['lat' => 35.6764, 'lng' => 139.6500],
            'South Korea' => ['lat' => 37.5665, 'lng' => 126.9780],
            'India' => ['lat' => 28.6139, 'lng' => 77.2090],
            'United States' => ['lat' => 38.9072, 'lng' => -77.0369],
            'Germany' => ['lat' => 52.5200, 'lng' => 13.4050],
            'Netherlands' => ['lat' => 52.3676, 'lng' => 4.9041],
            'Australia' => ['lat' => -35.2809, 'lng' => 149.1300],
            'United Kingdom' => ['lat' => 51.5074, 'lng' => -0.1278],
            'United Arab Emirates' => ['lat' => 24.4539, 'lng' => 54.3773],
        ];

        if (isset($capitalCoords[$country->name])) {
            Log::info('Using capital coordinates fallback for weather import', [
                'country' => $country->name,
                'capital' => $country->capital
            ]);
            return $capitalCoords[$country->name];
        }

        // Try coordinates of capital using simple geocoding or country info
        return null;
    }

    /**
     * Generate weather alerts based on custom rules.
     */
    public function generateWeatherAlerts(Country $country, array $current): array
    {
        // Delete previous alerts for this country to prevent duplication
        \App\Models\WeatherAlert::where('country_id', $country->id)->delete();

        $alerts = [];
        $temp = $current['temperature_2m'] ?? null;
        $rain = $current['rain'] ?? null;
        $windSpeed = $current['wind_speed_10m'] ?? null; // km/h (Note: 1 m/s = 3.6 km/h)
        $code = $current['weather_code'] ?? null;
        $humidity = $current['relative_humidity_2m'] ?? null;
        $cloud = $current['cloud_cover'] ?? null;

        // Rule 1: Temperature Extreme (> 40°C or < -5°C)
        if ($temp !== null) {
            if ($temp > 40) {
                $alerts[] = [
                    'severity' => 'HIGH',
                    'title' => 'Extreme Heat Warning',
                    'description' => "Dangerously high temperature ({$temp}°C) detected. Restrict outdoor transport operations.",
                    'temperature' => $temp,
                    'weather_condition' => 'Extreme Heat',
                ];
            } elseif ($temp < -5) {
                $alerts[] = [
                    'severity' => 'HIGH',
                    'title' => 'Extreme Cold Warning',
                    'description' => "Dangerously low temperature ({$temp}°C) detected. Risk of freeze damage to liquid cargo and engine failures.",
                    'temperature' => $temp,
                    'weather_condition' => 'Extreme Cold',
                ];
            }
        }

        // Rule 2: Thunderstorms (WMO codes 95, 96, 99)
        if ($code !== null && in_array($code, [95, 96, 99])) {
            $severity = ($code === 99) ? 'CRITICAL' : 'HIGH';
            $alerts[] = [
                'severity' => $severity,
                'title' => ($code === 99) ? 'Severe Thunderstorm & Hail Warning' : 'Thunderstorm Alert',
                'description' => 'Heavy thunderstorms and lightning detected. Significant risk of supply chain disruptions.',
                'temperature' => $temp,
                'weather_condition' => 'Thunderstorm',
            ];
        }

        // Rule 3: Heavy / Extreme Rain
        if ($rain !== null && $rain > 10.0) {
            $alerts[] = [
                'severity' => 'HIGH',
                'title' => 'Extreme Rainfall Warning',
                'description' => "Severe downpour ({$rain} mm/h) detected. Local flash flooding risk on key logistics routes.",
                'temperature' => $temp,
                'weather_condition' => 'Extreme Rain',
            ];
        } elseif ($code !== null && in_array($code, [65, 82])) {
            $alerts[] = [
                'severity' => 'HIGH',
                'title' => 'Heavy Rainfall Alert',
                'description' => 'Heavy precipitation detected. Road closures and drainage bottlenecks expected.',
                'temperature' => $temp,
                'weather_condition' => 'Heavy Rain',
            ];
        }

        // Rule 4: Snow (WMO codes 71, 73, 75, 77, 85, 86)
        if ($code !== null && in_array($code, [71, 73, 75, 77, 85, 86])) {
            $alerts[] = [
                'severity' => 'MEDIUM',
                'title' => 'Snowfall Warning',
                'description' => 'Snowfall detected. Expect transport delays and hazardous driving conditions.',
                'temperature' => $temp,
                'weather_condition' => 'Snow',
            ];
        }

        // Rule 5: Fog / Mist (WMO codes 45, 48)
        if ($code !== null && in_array($code, [45, 48])) {
            $alerts[] = [
                'severity' => 'LOW',
                'title' => 'Fog Advisory',
                'description' => 'Fog and low visibility detected. Reduced speed limits for trucks and port pilots.',
                'temperature' => $temp,
                'weather_condition' => 'Fog',
            ];
        }

        // Rule 6: Strong Wind (Wind speed > 15 m/s = 54 km/h -> HIGH, > 10 m/s = 36 km/h -> MEDIUM)
        if ($windSpeed !== null) {
            if ($windSpeed > 54.0) {
                $alerts[] = [
                    'severity' => 'HIGH',
                    'title' => 'Gale Warning',
                    'description' => "High gale winds ({$windSpeed} km/h) detected. Port cargo handling and crane operations suspended.",
                    'temperature' => $temp,
                    'weather_condition' => 'Strong Wind',
                ];
            } elseif ($windSpeed > 36.0) {
                $alerts[] = [
                    'severity' => 'MEDIUM',
                    'title' => 'High Wind Advisory',
                    'description' => "Wind speeds of {$windSpeed} km/h detected. Secure loose cargo and yard containers.",
                    'temperature' => $temp,
                    'weather_condition' => 'Moderate Wind',
                ];
            }
        }

        // Rule 7: Humidity Extreme (> 95%)
        if ($humidity !== null && $humidity > 95) {
            $alerts[] = [
                'severity' => 'MEDIUM',
                'title' => 'High Humidity Warning',
                'description' => "Relative humidity levels are extremely high ({$humidity}%). High condensation/sweat risk for dry bulk cargo.",
                'temperature' => $temp,
                'weather_condition' => 'High Humidity',
            ];
        }

        // Rule 8: Low Visibility Warning (cloud cover > 90% and heavy rain)
        if ($cloud !== null && $cloud > 90 && $rain !== null && $rain > 5.0) {
            $alerts[] = [
                'severity' => 'MEDIUM',
                'title' => 'Low Visibility Warning',
                'description' => 'Low visibility due to severe cloud cover and precipitation.',
                'temperature' => $temp,
                'weather_condition' => 'Overcast & Rain',
            ];
        }

        // Save generated alerts to database
        foreach ($alerts as $alertData) {
            \App\Models\WeatherAlert::create([
                'country_id' => $country->id,
                'severity' => $alertData['severity'],
                'title' => $alertData['title'],
                'description' => $alertData['description'],
                'temperature' => $alertData['temperature'],
                'weather_condition' => $alertData['weather_condition'],
                'generated_at' => now(),
                'expires_at' => now()->addHours(6),
            ]);
        }

        return $alerts;
    }
}