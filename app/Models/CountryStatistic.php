<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryStatistic extends Model
{
    protected $fillable = [
        'country_id',
        'year',
        'gdp',
        'inflation',
        'export_value',
        'import_value',
        'population',
        'data_source',
    ];

    protected $casts = [
        'year' => 'integer',
        'gdp' => 'decimal:2',
        'inflation' => 'decimal:2',
        'export_value' => 'decimal:2',
        'import_value' => 'decimal:2',
        'population' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}