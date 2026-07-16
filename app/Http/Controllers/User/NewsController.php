<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use App\Services\NewsService;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(
        NewsService $newsService
    ): View
    {
        return view('user.news.index', [

            'news' => $newsService->getNews(request()->all()),

            'countries' => $newsService->getCountries(),

        ]);
    }

    public function show(
        NewsCache $news
    ): View
    {
        $news->load('country');

        return view('user.news.show', compact('news'));
    }
}