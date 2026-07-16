<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskHistory extends Model
{
    protected $fillable = [
        'country_id',
        'weather_risk',
        'inflation_risk',
        'news_risk',
        'currency_risk',
        'total_risk_score',
        'risk_level',
        'calculated_at',
    ];

    protected $casts = [
        'weather_risk' => 'decimal:2',
        'inflation_risk' => 'decimal:2',
        'news_risk' => 'decimal:2',
        'currency_risk' => 'decimal:2',
        'total_risk_score' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}