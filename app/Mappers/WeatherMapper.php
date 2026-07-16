<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\WeatherDTO;
use App\Models\Country;

class WeatherMapper
{
    public static function fromApi(
        Country $country,
        array $weather,
        array $alerts = []
    ): WeatherDTO {

        $current = $weather['current'] ?? [];
        $code = $current['weather_code'] ?? 0;
        $cond = self::getWeatherCondition($code);

        return new WeatherDTO(
            countryId: $country->id,
            temperature: $current['temperature_2m'] ?? null,
            windSpeed: $current['wind_speed_10m'] ?? null,
            rain: $current['rain'] ?? null,
            weatherCode: $code,
            recordedAt: now()->toDateTimeString(),
            humidity: $current['relative_humidity_2m'] ?? null,
            pressure: $current['surface_pressure'] ?? null,
            weatherMain: $cond['Main'],
            weatherDescription: $cond['Desc'],
            cloud: $current['cloud_cover'] ?? null,
            alerts: $alerts
        );
    }

    /**
     * Map WMO weather code to readable conditions.
     */
    public static function getWeatherCondition(int $code): array
    {
        $map = [
            0 => ['Main' => 'Clear Sky', 'Desc' => 'Cloudless clear sky'],
            1 => ['Main' => 'Mainly Clear', 'Desc' => 'Mainly clear sky'],
            2 => ['Main' => 'Partly Cloudy', 'Desc' => 'Partly cloudy sky'],
            3 => ['Main' => 'Overcast', 'Desc' => 'Overcast skies'],
            45 => ['Main' => 'Fog', 'Desc' => 'Dense fog conditions'],
            48 => ['Main' => 'Depositing Rime Fog', 'Desc' => 'Freezing rime fog'],
            51 => ['Main' => 'Light Drizzle', 'Desc' => 'Light intensity drizzle'],
            53 => ['Main' => 'Moderate Drizzle', 'Desc' => 'Moderate intensity drizzle'],
            55 => ['Main' => 'Heavy Drizzle', 'Desc' => 'Dense intensity drizzle'],
            56 => ['Main' => 'Light Freezing Drizzle', 'Desc' => 'Light intensity freezing drizzle'],
            57 => ['Main' => 'Heavy Freezing Drizzle', 'Desc' => 'Dense intensity freezing drizzle'],
            61 => ['Main' => 'Light Rain', 'Desc' => 'Light rain showers'],
            63 => ['Main' => 'Moderate Rain', 'Desc' => 'Moderate continuous rain'],
            65 => ['Main' => 'Heavy Rain', 'Desc' => 'Continuous heavy rain'],
            66 => ['Main' => 'Light Freezing Rain', 'Desc' => 'Light freezing rain'],
            67 => ['Main' => 'Heavy Freezing Rain', 'Desc' => 'Heavy freezing rain'],
            71 => ['Main' => 'Light Snowfall', 'Desc' => 'Light snow accumulation'],
            73 => ['Main' => 'Moderate Snowfall', 'Desc' => 'Moderate snow accumulation'],
            75 => ['Main' => 'Heavy Snowfall', 'Desc' => 'Heavy snowfall accumulation'],
            77 => ['Main' => 'Snow Grains', 'Desc' => 'Granular snow particles'],
            80 => ['Main' => 'Light Rain Showers', 'Desc' => 'Light passing rain showers'],
            81 => ['Main' => 'Moderate Rain Showers', 'Desc' => 'Moderate passing rain showers'],
            82 => ['Main' => 'Violent Rain Showers', 'Desc' => 'Violent downpour and rain showers'],
            85 => ['Main' => 'Light Snow Showers', 'Desc' => 'Light passing snow showers'],
            86 => ['Main' => 'Heavy Snow Showers', 'Desc' => 'Heavy passing snow showers'],
            95 => ['Main' => 'Thunderstorm', 'Desc' => 'Thunderstorm with lightning'],
            96 => ['Main' => 'Thunderstorm & Light Hail', 'Desc' => 'Thunderstorm with light hail'],
            99 => ['Main' => 'Severe Thunderstorm & Heavy Hail', 'Desc' => 'Severe thunderstorm with heavy hail'],
        ];

        return $map[$code] ?? ['Main' => 'Unknown', 'Desc' => 'Unknown weather condition'];
    }
}