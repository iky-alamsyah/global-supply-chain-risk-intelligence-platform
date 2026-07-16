<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\NewsMapper;
use App\Repositories\CountryRepository;
use App\Repositories\NewsRepository;
use App\Services\API\NewsDataService;
use App\Services\Sentiment\LexiconSentimentService;
use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Log;

class NewsImportService
{
    public function __construct(
        protected NewsDataService $newsDataService,
        protected CountryRepository $countryRepository,
        protected NewsRepository $newsRepository,
        protected LexiconSentimentService $sentimentService,
    ) {
    }

    /**
     * Import news for all active countries.
     */
    public function import(): array
    {
        $success = 0;
        $failed = 0;

        $countries = $this->countryRepository->activeCountries();

        foreach ($countries as $country) {
            try {
                $this->importForCountry($country);
                $success++;
            } catch (\Throwable $e) {
                Log::error('News Import Failed for Country', [
                    'country' => $country->name,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * Import news for a specific country using fallback search terms.
     */
    public function importForCountry(Country $country): void
    {
        $searchTerms = $this->getCountrySearchTerms($country);
        $foundNews = false;

        foreach ($searchTerms as $term) {
            // Optimized query: exact country name/alias AND trade/supply chain keywords
            $query = sprintf('"%s" AND (logistics OR shipping OR port OR "supply chain" OR trade OR economy)', $term);

            try {
                Log::info('Searching news for country with term', [
                    'country' => $country->name,
                    'term' => $term,
                    'query' => $query
                ]);

                $response = $this->newsDataService->search($query);

                if (!empty($response['results'])) {
                    foreach ($response['results'] as $article) {
                        // Prevent duplicate news within database (check by URL)
                        $exists = NewsCache::where('url', $article['link'])->exists();
                        if ($exists) {
                            continue;
                        }

                        // Analyze sentiment
                        $analysis = $this->sentimentService->analyze(
                            $article['title'] ?? '',
                            $article['description'] ?? null
                        );

                        // Map & Save
                        $dto = NewsMapper::fromApi($country, $article, $analysis);
                        $this->newsRepository->updateOrCreate($dto);
                    }

                    $foundNews = true;
                    Log::info('News found and imported for country', [
                        'country' => $country->name,
                        'term' => $term,
                        'count' => count($response['results'])
                    ]);

                    break; // Stop looking after successful import
                }
            } catch (\Throwable $e) {
                Log::warning('News API search failed for term', [
                    'country' => $country->name,
                    'term' => $term,
                    'error' => $e->getMessage()
                ]);
                // Continue to next fallback term
            }
        }

        if (!$foundNews) {
            Log::warning('No news articles could be found/imported for country after all fallbacks', [
                'country' => $country->name,
                'search_terms' => $searchTerms
            ]);
        }
    }

    /**
     * Generate fallback search terms for a country.
     */
    protected function getCountrySearchTerms(Country $country): array
    {
        $terms = [];

        // 1. Common Name
        if (!empty($country->name)) {
            $terms[] = trim($country->name);
        }

        // 2. Country Alias / Alternatives (Predefined dictionary)
        $aliases = [
            'United States' => ['USA', 'US', 'United States of America'],
            'United Kingdom' => ['UK', 'Britain', 'Great Britain'],
            'South Korea' => ['Korea', 'Republic of Korea'],
            'Vietnam' => ['Viet Nam'],
            'Netherlands' => ['Holland'],
            'United Arab Emirates' => ['UAE'],
        ];

        if (isset($aliases[$country->name])) {
            foreach ($aliases[$country->name] as $alias) {
                $terms[] = trim($alias);
            }
        }

        // 3. Official Name
        if (!empty($country->official_name) && $country->official_name !== $country->name) {
            $terms[] = trim($country->official_name);
        }

        // 4. Capital City
        if (!empty($country->capital)) {
            $terms[] = trim($country->capital);
        }

        // 5. ISO3
        if (!empty($country->iso3)) {
            $terms[] = trim($country->iso3);
        }

        // 6. ISO2
        if (!empty($country->iso2)) {
            $terms[] = trim($country->iso2);
        }

        return array_values(array_unique(array_filter($terms)));
    }
}