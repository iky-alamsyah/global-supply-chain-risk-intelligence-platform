<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = [
        'country_id',
        'temperature',
        'humidity',
        'pressure',
        'weather_main',
        'weather_description',
        'cloud',
        'weather_code',
        'rainfall',
        'wind_speed',
        'storm_probability',
        'weather_risk_score',
        'weather_time',
        'expires_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'integer',
        'pressure' => 'float',
        'cloud' => 'integer',
        'weather_code' => 'integer',
        'rainfall' => 'float',
        'wind_speed' => 'float',
        'storm_probability' => 'float',
        'weather_risk_score' => 'float',
        'weather_time' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}