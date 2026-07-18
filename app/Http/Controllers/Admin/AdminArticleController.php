<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $categoryFilter = $request->input('category');

        // Stats
        $totalArticles = Article::count();
        $draftCount = Article::where('status', 'draft')->count();
        $publishedCount = Article::where('status', 'published')->count();
        $archivedCount = Article::where('status', 'archived')->count();

        // Query
        $query = Article::with(['author', 'country']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.articles.index', compact(
            'articles', 'totalArticles', 'draftCount', 'publishedCount', 'archivedCount',
            'search', 'statusFilter', 'categoryFilter'
        ));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.articles.create', compact('countries'));
    }

    /**
     * Store a newly created article in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'], // 2MB max
            'category' => ['required', 'string', Rule::in(['economy', 'trade', 'shipping', 'logistics'])],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'country_id' => ['nullable', 'exists:countries,id'],
        ]);

        $slug = $this->generateUniqueSlug($request->input('title'));

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        }

        $publishedAt = null;
        if ($request->input('status') === 'published') {
            $publishedAt = now();
        }

        $article = Article::create([
            'author_id' => auth()->id() ?? 1, // Fallback if no auth (e.g. testing)
            'country_id' => $request->input('country_id'),
            'title' => $request->input('title'),
            'slug' => $slug,
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'thumbnail' => $thumbnailPath,
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'published_at' => $publishedAt,
        ]);

        $this->syncToNewsFeed($article);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'countries'));
    }

    /**
     * Update the specified article in database.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'category' => ['required', 'string', Rule::in(['economy', 'trade', 'shipping', 'logistics'])],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'country_id' => ['nullable', 'exists:countries,id'],
        ]);

        // Regenerate slug if title changes
        $slug = $article->slug;
        if ($request->input('title') !== $article->title) {
            $slug = $this->generateUniqueSlug($request->input('title'), $article->id);
        }

        $thumbnailPath = $article->thumbnail;
        if ($request->hasFile('thumbnail')) {
            // Delete old one if exists
            if ($thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        }

        $publishedAt = $article->published_at;
        if ($request->input('status') === 'published' && !$publishedAt) {
            $publishedAt = now();
        } elseif ($request->input('status') !== 'published') {
            $publishedAt = null;
        }

        $article->update([
            'country_id' => $request->input('country_id'),
            'title' => $request->input('title'),
            'slug' => $slug,
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'thumbnail' => $thumbnailPath,
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'published_at' => $publishedAt,
        ]);

        $this->syncToNewsFeed($article);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article from database.
     */
    public function destroy(Article $article): RedirectResponse
    {
        // Delete thumbnail
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }

        // Delete from News Feed cache
        NewsCache::where('url', url('/articles/' . $article->slug))->delete();

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }

    /**
     * Render the article for the public view (User & Preview).
     */
    public function publicShow(Request $request, string $slug): View
    {
        $article = Article::with(['author', 'country'])->where('slug', $slug)->firstOrFail();

        // Check if draft/archived and not authorized
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
        if ($article->status !== 'published' && !$isAdmin) {
            abort(404, 'Article not published.');
        }

        return view('user.news.show_article', compact('article'));
    }

    /**
     * Synchronize article with news_cache table.
     */
    protected function syncToNewsFeed(Article $article): void
    {
        $url = url('/articles/' . $article->slug);

        if ($article->status === 'published') {
            $countryId = $article->country_id;
            if (!$countryId) {
                // If country is not selected, link it to the first country in the registry
                $countryId = Country::first()?->id;
            }

            NewsCache::updateOrCreate(
                [
                    'url' => $url,
                ],
                [
                    'country_id' => $countryId,
                    'title' => $article->title,
                    'description' => $article->summary,
                    'content' => $article->content,
                    'source' => 'GSCRIP Editor',
                    'api_source' => 'GSCRIP',
                    'author' => $article->author->name ?? 'Admin',
                    'image_url' => $article->thumbnail ? asset('storage/' . $article->thumbnail) : null,
                    'category' => $article->category,
                    'published_at' => $article->published_at ?? now(),
                    'expires_at' => null,
                ]
            );
        } else {
            // Delete if draft or archived
            NewsCache::where('url', $url)->delete();
        }
    }

    /**
     * Generate unique slug helper.
     */
    protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $query = Article::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
