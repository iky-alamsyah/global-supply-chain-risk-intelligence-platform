<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use App\Models\CountryRiskScore;
use App\Models\NewsCache;
use App\Services\API\OpenMeteoService;
use App\Services\API\NewsDataService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ShipmentEstimationService
{
    public function __construct(
        protected OpenMeteoService $openMeteo,
        protected NewsDataService $newsData,
        protected RiskEngineService $riskEngine,
    ) {
    }

    /**
     * Get active countries list for user selection.
     */
    public function getActiveCountries(): array
    {
        return Country::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'flag', 'iso2', 'iso3'])
            ->toArray();
    }

    /**
     * Perform the shipment route calculation.
     */
    public function calculateEstimation(array $data): array
    {
        $originCountryId = (int) $data['origin_country_id'];
        $destCountryId = (int) $data['dest_country_id'];
        $cargoType = $data['cargo_type'];
        $speedSetting = $data['ship_speed']; // 'slow', 'normal', 'fast'

        // Determine Speed in km/h
        $speed = match ($speedSetting) {
            'slow' => 20.0,
            'fast' => 40.0,
            default => 30.0,
        };

        // Fetch countries
        $originCountry = Country::findOrFail($originCountryId);
        $destCountry = Country::findOrFail($destCountryId);

        // Fetch primary ports
        $originPort = Port::where('country_id', $originCountryId)->orderBy('id')->first();
        $destPort = Port::where('country_id', $destCountryId)->orderBy('id')->first();

        if (!$originPort) {
            throw new \Exception("Origin country does not have any ports in our database.");
        }
        if (!$destPort) {
            throw new \Exception("Destination country does not have any ports in our database.");
        }

        $originCoords = ['lat' => (float) $originPort->latitude, 'lng' => (float) $originPort->longitude];
        $destCoords = ['lat' => (float) $destPort->latitude, 'lng' => (float) $destPort->longitude];

        // Generate Realistic Marine Waypoints
        $waypoints = $this->generateWaypoints($originCoords, $destCoords, $originCountry, $destCountry);

        // Calculate Haversine Distance along path
        $distance = 0.0;
        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $distance += $this->haversineDistance(
                $waypoints[$i]['lat'], $waypoints[$i]['lng'],
                $waypoints[$i + 1]['lat'], $waypoints[$i + 1]['lng']
            );
        }

        // Round distance
        $distance = round($distance, 1);

        // Calculate Duration & ETA
        $durationHours = $distance / $speed;
        $days = (int) floor($durationHours / 24);
        $remainingHours = (int) round($durationHours % 24);
        $durationString = "{$days} Hari {$remainingHours} Jam";

        $departureTime = now();
        $arrivalTime = now()->addHours($durationHours);

        // Weather origin & destination
        $originWeather = $this->getWeatherData($originCoords['lat'], $originCoords['lng']);
        $destWeather = $this->getWeatherData($destCoords['lat'], $destCoords['lng']);

        // Determine potential delay based on weather conditions
        $potentialDelay = $originWeather['potential_delay'] || $destWeather['potential_delay'];

        // Get Risk scores
        $originRisk = $this->getCountryRisk($originCountry);
        $destRisk = $this->getCountryRisk($destCountry);

        // Get disruption news feed (NewsData API fallback to NewsCache)
        $newsFeed = $this->getDisruptionNews();
        $hasDisruptionNews = count($newsFeed) > 0;

        return [
            'origin_port' => [
                'name' => $originPort->port_name,
                'code' => $originPort->port_code,
                'city' => $originPort->city,
                'country' => $originCountry->name,
                'lat' => $originCoords['lat'],
                'lng' => $originCoords['lng'],
            ],
            'dest_port' => [
                'name' => $destPort->port_name,
                'code' => $destPort->port_code,
                'city' => $destPort->city,
                'country' => $destCountry->name,
                'lat' => $destCoords['lat'],
                'lng' => $destCoords['lng'],
            ],
            'cargo_type' => $cargoType,
            'speed_kmh' => $speed,
            'distance_km' => $distance,
            'duration' => $durationString,
            'departure' => $this->formatIndonesianDate($departureTime),
            'arrival' => $this->formatIndonesianDate($arrivalTime),
            'origin_weather' => $originWeather,
            'dest_weather' => $destWeather,
            'potential_delay' => $potentialDelay,
            'origin_risk' => $originRisk,
            'dest_risk' => $destRisk,
            'news' => $newsFeed,
            'has_disruption_news' => $hasDisruptionNews,
            'waypoints' => $waypoints,
        ];
    }

    /**
     * Compute Haversine distance.
     */
    protected function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Date formatter (Indonesian terms)
     */
    protected function formatIndonesianDate(Carbon $date): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return $date->format('d') . ' ' . $months[$date->month] . ' ' . $date->format('Y H:i');
    }

    /**
     * Get Country risk score
     */
    protected function getCountryRisk(Country $country): array
    {
        $risk = CountryRiskScore::where('country_id', $country->id)->first();

        // If not found in DB, try to calculate on-the-fly
        if (!$risk) {
            try {
                $dto = $this->riskEngine->calculate($country);
                return [
                    'score' => round($dto->riskScore, 1),
                    'level' => $dto->riskLevel, // 'HIGH', 'MEDIUM', 'LOW'
                ];
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return [
            'score' => $risk ? round((float) $risk->risk_score, 1) : 50.0,
            'level' => $risk ? strtoupper($risk->risk_level) : 'MEDIUM',
        ];
    }

    /**
     * Retrieve OpenMeteo weather
     */
    protected function getWeatherData(float $lat, float $lng): array
    {
        try {
            $data = $this->openMeteo->current($lat, $lng);
            if (!empty($data['current'])) {
                $current = $data['current'];
                $windSpeed = (float) ($current['wind_speed_10m'] ?? 0);
                $rain = (float) ($current['rain'] ?? 0);
                $code = (int) ($current['weather_code'] ?? 0);

                $condition = $this->getWmoCondition($code);
                $isDelay = ($windSpeed > 40.0) || ($rain > 10.0) || in_array($code, [95, 96, 99]);

                return [
                    'temperature' => round((float) ($current['temperature_2m'] ?? 25.0), 1),
                    'humidity' => (int) ($current['relative_humidity_2m'] ?? 70),
                    'wind_speed' => round($windSpeed, 1),
                    'condition' => $condition,
                    'potential_delay' => $isDelay,
                ];
            }
        } catch (\Throwable $e) {
            // Ignore API exceptions
        }

        return [
            'temperature' => 26.0,
            'humidity' => 78,
            'wind_speed' => 14.2,
            'condition' => 'Partly Cloudy',
            'potential_delay' => false,
        ];
    }

    /**
     * WMO Code mapper helper.
     */
    protected function getWmoCondition(int $code): string
    {
        return match ($code) {
            0 => 'Clear Sky',
            1, 2, 3 => 'Partly Cloudy',
            45, 48 => 'Foggy',
            51, 53, 55 => 'Drizzle',
            61, 63 => 'Rainy',
            65 => 'Heavy Rain',
            71, 73, 75 => 'Snowy',
            80, 81, 82 => 'Rain Showers',
            95, 96, 99 => 'Storm / Thunderstorm',
            default => 'Overcast',
        };
    }

    /**
     * News disruption check (NewsData API with local NewsCache fallback).
     */
    protected function getDisruptionNews(): array
    {
        $newsFeed = [];

        // 1. Try NewsData API with combined search query
        try {
            $response = $this->newsData->search('shipping OR port OR storm OR strike OR conflict OR typhoon', 'en', 3);
            if (!empty($response['results'])) {
                foreach ($response['results'] as $item) {
                    $newsFeed[] = [
                        'title' => $item['title'],
                        'url' => $item['link'] ?? '#',
                        'source' => $item['source_id'] ?? 'NewsAPI',
                        'published_at' => $item['pubDate'] ?? now()->toDateTimeString(),
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore API exceptions and fall back
        }

        // 2. If API didn't return any news, query local NewsCache table
        if (empty($newsFeed)) {
            $localNews = NewsCache::where(function ($q) {
                $q->where('title', 'like', '%shipping%')
                  ->orWhere('title', 'like', '%port%')
                  ->orWhere('title', 'like', '%storm%')
                  ->orWhere('title', 'like', '%strike%')
                  ->orWhere('title', 'like', '%conflict%')
                  ->orWhere('title', 'like', '%typhoon%');
            })
            ->latest('published_at')
            ->take(3)
            ->get();

            foreach ($localNews as $n) {
                $newsFeed[] = [
                    'title' => $n->title,
                    'url' => $n->url,
                    'source' => $n->source,
                    'published_at' => $n->published_at ? $n->published_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            }
        }

        return $newsFeed;
    }

    /**
     * Predefined Marine Waypoints compiler for sea-accurate paths.
     */
    protected function generateWaypoints(array $origin, array $dest, Country $origCountry, Country $destCountry): array
    {
        $points = [];
        $points[] = $origin;

        $oName = $origCountry->name;
        $dName = $destCountry->name;

        // Waypoints bank
        $waypointBank = [
            'SuezCanal' => ['lat' => 29.9, 'lng' => 32.5],
            'BabElMandeb' => ['lat' => 12.6, 'lng' => 43.3],
            'Gibraltar' => ['lat' => 35.9, 'lng' => -5.6],
            'MalaccaWest' => ['lat' => 6.0, 'lng' => 95.0],
            'MalaccaEast' => ['lat' => 1.3, 'lng' => 103.5],
            'SundaStrait' => ['lat' => -6.1, 'lng' => 105.8],
            'LombokStrait' => ['lat' => -8.4, 'lng' => 115.7],
            'SouthChinaSeaS' => ['lat' => 5.0, 'lng' => 108.0],
            'SouthChinaSeaN' => ['lat' => 15.0, 'lng' => 115.0],
            'EastChinaSea' => ['lat' => 28.0, 'lng' => 123.0],
            'PhilippineSea' => ['lat' => 20.0, 'lng' => 130.0],
            'PacificWest' => ['lat' => 30.0, 'lng' => 150.0],
            'PacificCentral' => ['lat' => 35.0, 'lng' => 180.0],
            'PacificEast' => ['lat' => 35.0, 'lng' => -130.0],
            'PanamaCanal' => ['lat' => 9.0, 'lng' => -79.8],
            'Caribbean' => ['lat' => 15.0, 'lng' => -75.0],
            'EnglishChannel' => ['lat' => 50.3, 'lng' => -0.5],
            'GulfOfOman' => ['lat' => 24.0, 'lng' => 58.5],
            'ArabianSea' => ['lat' => 15.0, 'lng' => 65.0],
            'IndianOcean' => ['lat' => 5.0, 'lng' => 80.0],
        ];

        // 1. Southeast Asia <-> East Asia (Indonesia, SG, MY, TH, VN <-> China, JP, KR)
        $isSEAsia = fn($name) => in_array($name, ['Indonesia', 'Singapore', 'Malaysia', 'Thailand', 'Vietnam']);
        $isEastAsia = fn($name) => in_array($name, ['China', 'Japan', 'South Korea']);

        if (($isSEAsia($oName) && $isEastAsia($dName)) || ($isEastAsia($oName) && $isSEAsia($dName))) {
            // Forward route path
            if ($isSEAsia($oName)) {
                $points[] = $waypointBank['SouthChinaSeaS'];
                $points[] = $waypointBank['SouthChinaSeaN'];
                $points[] = $waypointBank['EastChinaSea'];
            } else {
                $points[] = $waypointBank['EastChinaSea'];
                $points[] = $waypointBank['SouthChinaSeaN'];
                $points[] = $waypointBank['SouthChinaSeaS'];
            }
        }
        // 2. Southeast/East Asia <-> Europe (Germany, Netherlands, UK)
        elseif (($isSEAsia($oName) || $isEastAsia($oName)) && in_array($dName, ['Germany', 'Netherlands', 'United Kingdom'])) {
            if ($isEastAsia($oName)) {
                $points[] = $waypointBank['EastChinaSea'];
                $points[] = $waypointBank['SouthChinaSeaN'];
            }
            $points[] = $waypointBank['SouthChinaSeaS'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['MalaccaWest'];
            $points[] = $waypointBank['IndianOcean'];
            $points[] = $waypointBank['BabElMandeb'];
            $points[] = $waypointBank['SuezCanal'];
            $points[] = $waypointBank['Gibraltar'];
            $points[] = $waypointBank['EnglishChannel'];
        }
        // 3. Europe <-> Southeast/East Asia (Germany, Netherlands, UK <-> Southeast/East Asia)
        elseif (in_array($oName, ['Germany', 'Netherlands', 'United Kingdom']) && ($isSEAsia($dName) || $isEastAsia($dName))) {
            $points[] = $waypointBank['EnglishChannel'];
            $points[] = $waypointBank['Gibraltar'];
            $points[] = $waypointBank['SuezCanal'];
            $points[] = $waypointBank['BabElMandeb'];
            $points[] = $waypointBank['IndianOcean'];
            $points[] = $waypointBank['MalaccaWest'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['SouthChinaSeaS'];
            if ($isEastAsia($dName)) {
                $points[] = $waypointBank['SouthChinaSeaN'];
                $points[] = $waypointBank['EastChinaSea'];
            }
        }
        // 4. Middle East (UAE) <-> Europe
        elseif ($oName === 'United Arab Emirates' && in_array($dName, ['Germany', 'Netherlands', 'United Kingdom'])) {
            $points[] = $waypointBank['GulfOfOman'];
            $points[] = $waypointBank['BabElMandeb'];
            $points[] = $waypointBank['SuezCanal'];
            $points[] = $waypointBank['Gibraltar'];
            $points[] = $waypointBank['EnglishChannel'];
        }
        // 5. Europe <-> Middle East (UAE)
        elseif (in_array($oName, ['Germany', 'Netherlands', 'United Kingdom']) && $dName === 'United Arab Emirates') {
            $points[] = $waypointBank['EnglishChannel'];
            $points[] = $waypointBank['Gibraltar'];
            $points[] = $waypointBank['SuezCanal'];
            $points[] = $waypointBank['BabElMandeb'];
            $points[] = $waypointBank['GulfOfOman'];
        }
        // 6. Middle East (UAE) <-> East/Southeast Asia
        elseif ($oName === 'United Arab Emirates' && ($isSEAsia($dName) || $isEastAsia($dName))) {
            $points[] = $waypointBank['GulfOfOman'];
            $points[] = $waypointBank['IndianOcean'];
            $points[] = $waypointBank['MalaccaWest'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['SouthChinaSeaS'];
            if ($isEastAsia($dName)) {
                $points[] = $waypointBank['SouthChinaSeaN'];
                $points[] = $waypointBank['EastChinaSea'];
            }
        }
        // 7. East/Southeast Asia <-> Middle East (UAE)
        elseif (($isSEAsia($oName) || $isEastAsia($oName)) && $dName === 'United Arab Emirates') {
            if ($isEastAsia($oName)) {
                $points[] = $waypointBank['EastChinaSea'];
                $points[] = $waypointBank['SouthChinaSeaN'];
            }
            $points[] = $waypointBank['SouthChinaSeaS'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['MalaccaWest'];
            $points[] = $waypointBank['IndianOcean'];
            $points[] = $waypointBank['GulfOfOman'];
        }
        // 8. India <-> Southeast/East Asia
        elseif ($oName === 'India' && ($isSEAsia($dName) || $isEastAsia($dName))) {
            $points[] = $waypointBank['MalaccaWest'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['SouthChinaSeaS'];
            if ($isEastAsia($dName)) {
                $points[] = $waypointBank['SouthChinaSeaN'];
                $points[] = $waypointBank['EastChinaSea'];
            }
        }
        // 9. Southeast/East Asia <-> India
        elseif (($isSEAsia($oName) || $isEastAsia($oName)) && $dName === 'India') {
            if ($isEastAsia($oName)) {
                $points[] = $waypointBank['EastChinaSea'];
                $points[] = $waypointBank['SouthChinaSeaN'];
            }
            $points[] = $waypointBank['SouthChinaSeaS'];
            $points[] = $waypointBank['MalaccaEast'];
            $points[] = $waypointBank['MalaccaWest'];
        }
        // 10. Australia <-> Southeast Asia
        elseif ($oName === 'Australia' && $isSEAsia($dName)) {
            $points[] = $waypointBank['LombokStrait'];
            $points[] = $waypointBank['SouthChinaSeaS'];
        }
        // 11. Southeast Asia <-> Australia
        elseif ($isSEAsia($oName) && $dName === 'Australia') {
            $points[] = $waypointBank['SouthChinaSeaS'];
            $points[] = $waypointBank['LombokStrait'];
        }
        // 12. USA <-> East Asia (Pacific Crossing)
        elseif ($oName === 'United States' && $isEastAsia($dName)) {
            $points[] = $waypointBank['PacificEast'];
            $points[] = $waypointBank['PacificCentral'];
            $points[] = $waypointBank['PacificWest'];
            $points[] = $waypointBank['PhilippineSea'];
        }
        // 13. East Asia <-> USA (Pacific Crossing)
        elseif ($isEastAsia($oName) && $dName === 'United States') {
            $points[] = $waypointBank['PhilippineSea'];
            $points[] = $waypointBank['PacificWest'];
            $points[] = $waypointBank['PacificCentral'];
            $points[] = $waypointBank['PacificEast'];
        }
        // Default route fallback: if no direct match, add a slight oceanic curve
        else {
            $midLat = ($origin['lat'] + $dest['lat']) / 2;
            $midLng = ($origin['lng'] + $dest['lng']) / 2;
            
            // Adjust midpoints to look more like curves over oceans
            $points[] = ['lat' => $midLat + 3.0, 'lng' => $midLng - 4.0];
            $points[] = ['lat' => $midLat - 2.0, 'lng' => $midLng + 3.0];
        }

        $points[] = $dest;
        return $points;
    }
}
