<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyCache extends Model
{
    protected $table = 'currency_cache';

    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'exchange_rate',
        'previous_exchange_rate',
        'change_percentage',
        'currency_risk_score',
        'rate_time',
        'expires_at',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'previous_exchange_rate' => 'float',
        'change_percentage' => 'float',
        'currency_risk_score' => 'float',
        'rate_time' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}