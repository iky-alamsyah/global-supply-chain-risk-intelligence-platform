<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    protected $fillable = [
        'name',
        'official_name',
        'iso2',
        'iso3',
        'region',
        'subregion',
        'capital',
        'latitude',
        'longitude',
        'currency_code',
        'currency_name',
        'currency_symbol',
        'flag',
        'languages',
        'population',
        'is_active',
    ];

    protected $casts = [
        'languages' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'population' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Country Statistics (Historical)
     */
    public function statistics(): HasMany
    {
        return $this->hasMany(CountryStatistic::class);
    }

    /**
     * Ports
     */
    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    /**
     * Weather Cache (Historical)
     */
    public function weatherCaches(): HasMany
    {
        return $this->hasMany(WeatherCache::class);
    }

    /**
     * Weather Alerts
     */
    public function weatherAlerts(): HasMany
    {
        return $this->hasMany(WeatherAlert::class);
    }

    /**
     * Latest Weather Cache
     */
    public function latestWeather(): HasOne
    {
        return $this->hasOne(WeatherCache::class)->latestOfMany();
    }

    /**
     * Currency Cache (Historical)
     */
    public function currencyCaches(): HasMany
    {
        return $this->hasMany(CurrencyCache::class);
    }

    /**
     * Latest Currency Cache
     */
    public function latestCurrency(): HasOne
    {
        return $this->hasOne(CurrencyCache::class)->latestOfMany();
    }

    /**
     * Currency History
     */
    public function currencyHistories(): HasMany
    {
        return $this->hasMany(CurrencyHistory::class);
    }

    /**
     * News Cache
     */
    public function newsCaches(): HasMany
    {
        return $this->hasMany(NewsCache::class);
    }

    /**
     * Current Risk Score
     */
    public function riskScore(): HasOne
    {
        return $this->hasOne(CountryRiskScore::class);
    }

    /**
     * Risk History
     */
    public function riskHistories(): HasMany
    {
        return $this->hasMany(RiskHistory::class);
    }

    /**
     * Favorites
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Watchlists
     */
    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Articles
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}