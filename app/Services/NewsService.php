<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;

class NewsService
{
    public function getNews(array $filters = [])
    {
        $query = NewsCache::with('country');

        if (!empty($filters['search'])) {

            $query->where(function ($q) use ($filters) {

                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('summary', 'like', '%' . $filters['search'] . '%');

            });

        }

        if (!empty($filters['country'])) {

            $query->where('country_id', $filters['country']);

        }

        return $query
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function getCountries()
    {
        return Country::orderBy('name')->get();
    }
}