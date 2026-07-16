<?php

declare(strict_types=1);

namespace App\DTO;

class WeatherDTO
{
    public function __construct(
        public readonly int $countryId,
        public readonly ?float $temperature,
        public readonly ?float $windSpeed,
        public readonly ?float $rain,
        public readonly ?int $weatherCode,
        public readonly string $recordedAt,
        public readonly ?int $humidity = null,
        public readonly ?float $pressure = null,
        public readonly ?string $weatherMain = null,
        public readonly ?string $weatherDescription = null,
        public readonly ?int $cloud = null,
        public readonly array $alerts = [],
    ) {
    }

    public function toArray(): array
    {
        // Estimate storm probability based on rain and wind speed
        $stormProb = 0.0;
        if ($this->rain !== null) {
            $stormProb = min(100.0, $this->rain * 10);
        }
        if ($this->windSpeed !== null && $this->windSpeed > 30) {
            $stormProb = min(100.0, $stormProb + ($this->windSpeed - 30) * 2);
        }

        // Calculate weather risk score (0-100)
        $weatherRisk = 0.0;
        if ($this->temperature !== null) {
            if ($this->temperature < 5 || $this->temperature > 38) {
                $weatherRisk += 20;
            }
        }
        if ($this->windSpeed !== null) {
            $weatherRisk += min(40.0, $this->windSpeed * 0.8);
        }
        if ($this->rain !== null) {
            $weatherRisk += min(30.0, $this->rain * 4.0);
        }

        // Incorporate Weather Alerts severity into the Risk Score
        foreach ($this->alerts as $alert) {
            if ($alert['severity'] === 'CRITICAL') {
                $weatherRisk += 40;
            } elseif ($alert['severity'] === 'HIGH') {
                $weatherRisk += 25;
            } elseif ($alert['severity'] === 'MEDIUM') {
                $weatherRisk += 15;
            } elseif ($alert['severity'] === 'LOW') {
                $weatherRisk += 5;
            }
        }
        $weatherRisk = min(100.0, max(0.0, $weatherRisk));

        return [
            'country_id' => $this->countryId,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'weather_main' => $this->weatherMain,
            'weather_description' => $this->weatherDescription,
            'cloud' => $this->cloud,
            'weather_code' => $this->weatherCode,
            'wind_speed' => $this->windSpeed,
            'rainfall' => $this->rain,
            'storm_probability' => $stormProb,
            'weather_risk_score' => $weatherRisk,
            'weather_time' => $this->recordedAt ? \Carbon\Carbon::parse($this->recordedAt)->toDateTimeString() : now()->toDateTimeString(),
            'expires_at' => now()->addHours(2)->toDateTimeString(),
        ];
    }
}