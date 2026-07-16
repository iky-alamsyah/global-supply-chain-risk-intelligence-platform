<?php

declare(strict_types=1);

namespace App\DTO;

class NewsDTO
{
    public function __construct(
        public readonly int $countryId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $url,
        public readonly ?string $image,
        public readonly string $publishedAt,
        public readonly string $source,
        public readonly string $category,
        public readonly string $sentiment = 'neutral',
        public readonly float $newsRiskScore = 50.0,
        public readonly int $positiveScore = 0,
        public readonly int $negativeScore = 0,
    ) {
    }

    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'image_url' => $this->image,
            'published_at' => $this->publishedAt ? \Carbon\Carbon::parse($this->publishedAt)->toDateTimeString() : now()->toDateTimeString(),
            'source' => $this->source,
            'category' => $this->category,
            'sentiment' => $this->sentiment,
            'news_risk_score' => $this->newsRiskScore,
            'positive_score' => $this->positiveScore,
            'negative_score' => $this->negativeScore,
            'expires_at' => now()->addDays(2)->toDateTimeString(),
        ];
    }
}