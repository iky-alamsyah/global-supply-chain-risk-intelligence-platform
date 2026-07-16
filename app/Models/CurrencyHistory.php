<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyHistory extends Model
{
    protected $table = 'currency_histories';

    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'old_rate',
        'new_rate',
        'change_percentage',
    ];

    protected $casts = [
        'old_rate' => 'float',
        'new_rate' => 'float',
        'change_percentage' => 'float',
    ];

    /**
     * Country relation.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}