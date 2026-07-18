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
        'gdp' => 'float',
        'inflation' => 'float',
        'export_value' => 'float',
        'import_value' => 'float',
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