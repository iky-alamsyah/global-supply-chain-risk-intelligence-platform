<?php

declare(strict_types=1);

namespace App\Services\Sentiment;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

class LexiconSentimentService
{
    protected array $positiveWords = [];
    protected array $negativeWords = [];

    public function __construct()
    {
        // Constructor is kept free of database queries to avoid boot/test database errors
    }

    /**
     * Ensure lexicons are loaded, with complete exception protection.
     * If database or cache is not yet available (e.g. during application boot or compilation),
     * it falls back to empty arrays rather than failing.
     */
    protected function ensureLexiconsLoaded(): void
    {
        if (!empty($this->positiveWords) || !empty($this->negativeWords)) {
            return;
        }

        try {
            $this->positiveWords = Cache::remember('sentiment_positive_words', 3600, function () {
                return PositiveWord::pluck('word')
                    ->map(fn($w) => strtolower(trim($w)))
                    ->filter()
                    ->toArray();
            });
        } catch (\Throwable $e) {
            $this->positiveWords = [];
        }

        try {
            $this->negativeWords = Cache::remember('sentiment_negative_words', 3600, function () {
                return NegativeWord::pluck('word')
                    ->map(fn($w) => strtolower(trim($w)))
                    ->filter()
                    ->toArray();
            });
        } catch (\Throwable $e) {
            $this->negativeWords = [];
        }
    }

    /**
     * Analyze text and return scores and sentiment labels.
     */
    public function analyze(string $title, ?string $description = null): array
    {
        $this->ensureLexiconsLoaded();

        $text = strtolower($title . ' ' . ($description ?? ''));

        // Remove punctuation for clean matching
        $textClean = preg_replace('/[^\w\s\-]/u', ' ', $text);

        $posCount = 0;
        $negCount = 0;

        foreach ($this->positiveWords as $word) {
            // Find exact matches as word boundaries
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $posCount += preg_match_all($pattern, $textClean);
        }

        foreach ($this->negativeWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $negCount += preg_match_all($pattern, $textClean);
        }

        // Determine sentiment label
        $sentiment = 'neutral';
        if ($negCount > $posCount) {
            $sentiment = 'negative';
        } elseif ($posCount > $negCount) {
            $sentiment = 'positive';
        }

        // Calculate News Risk Score (0 - 100)
        // Neutral = 50
        // Negative = 50 + (neg - pos) * 15, max 100
        // Positive = 50 - (pos - neg) * 15, min 10
        if ($sentiment === 'negative') {
            $riskScore = 50 + (($negCount - $posCount) * 15);
            $riskScore = min(100.0, $riskScore);
        } elseif ($sentiment === 'positive') {
            $riskScore = 50 - (($posCount - $negCount) * 15);
            $riskScore = max(10.0, $riskScore);
        } else {
            $riskScore = 50.0;
        }

        return [
            'positive_score' => $posCount,
            'negative_score' => $negCount,
            'sentiment' => $sentiment,
            'news_risk_score' => (float) $riskScore,
        ];
    }
}
