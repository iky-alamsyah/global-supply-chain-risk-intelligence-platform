<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\NewsDTO;
use App\Models\Country;

class NewsMapper
{
    public static function fromApi(
        Country $country,
        array $article,
        ?array $analysis = null
    ): NewsDTO {

        return new NewsDTO(
            countryId: $country->id,
            title: $article['title'] ?? '',
            description: $article['description'] ?? null,
            url: $article['link'] ?? '',
            image: $article['image_url'] ?? null,
            publishedAt: $article['pubDate'] ?? now()->toDateTimeString(),
            source: $article['source_name'] ?? ($article['source_id'] ?? 'Unknown'),
            category: self::mapCategory($article['category'] ?? []),
            sentiment: $analysis['sentiment'] ?? 'neutral',
            newsRiskScore: $analysis['news_risk_score'] ?? 50.0,
            positiveScore: $analysis['positive_score'] ?? 0,
            negativeScore: $analysis['negative_score'] ?? 0
        );
    }

    /**
     * Map NewsData Category
     * menjadi kategori aplikasi
     */
    private static function mapCategory(array $categories): string
    {
        $categories = array_map('strtolower', $categories);

        if (
            in_array('shipping', $categories) ||
            in_array('maritime', $categories) ||
            in_array('transport', $categories)
        ) {

            return 'shipping';

        }

        if (
            in_array('logistics', $categories) ||
            in_array('freight', $categories)
        ) {

            return 'logistics';

        }

        if (
            in_array('business', $categories) ||
            in_array('economy', $categories) ||
            in_array('finance', $categories)
        ) {

            return 'economy';

        }

        return 'trade';
    }
}