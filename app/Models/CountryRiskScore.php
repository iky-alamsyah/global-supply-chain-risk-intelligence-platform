<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryRiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'gdp_score',
        'inflation_score',
        'weather_score',
        'currency_score',
        'news_score',
        'risk_score',
        'risk_level',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}