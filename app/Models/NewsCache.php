<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'content',
        'source',
        'author',
        'url',
        'image_url',
        'category',
        'published_at',
        'positive_score',
        'negative_score',
        'sentiment',
        'news_risk_score',
        'expires_at',
    ];

    protected $casts = [
    'positive_score' => 'float',
    'negative_score' => 'float',
    'news_risk_score' => 'float',
    'published_at' => 'datetime',
    'expires_at' => 'datetime',
];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}