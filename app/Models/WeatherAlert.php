<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherAlert extends Model
{
    protected $table = 'weather_alerts';

    protected $fillable = [
        'country_id',
        'severity',
        'title',
        'description',
        'temperature',
        'weather_condition',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Country relation.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}