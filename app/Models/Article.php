<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'author_id',
        'country_id',
        'title',
        'slug',
        'summary',
        'content',
        'meta_description',
        'thumbnail',
        'category',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Author / Creator
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Related Country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}