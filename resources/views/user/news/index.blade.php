@extends('layouts.dashboard')

@section('title', 'News Intelligence')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-newspaper me-2" style="color:#D97706;font-size:1.2rem;"></i>
            News Intelligence
        </h1>
        <p class="page-header-sub mb-0">
            Latest global supply chain news — {{ $news->total() }} articles indexed.
        </p>
    </div>
    <span class="badge d-flex align-items-center gap-1 px-3 py-2"
          style="background:rgba(22,163,74,.08);color:#16A34A;border:1px solid rgba(22,163,74,.2);border-radius:99px;font-size:.72rem;font-weight:700;height:fit-content;">
        <i class="bi bi-circle-fill" style="font-size:.45rem;animation:pulse-live 2s infinite;"></i>
        Live Feed
    </span>
</div>

{{-- Filter Bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Search News</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Keywords, headline..."
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ request()->url() }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filter by Country</label>
            <select name="country" class="form-select">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" @selected(request('country') == $country->id)>
                        {{ $country->flag }} {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search','country']))
                <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- News Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(217,119,6,.08);color:#D97706;">
            <i class="bi bi-newspaper"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Latest Intelligence</span>
        <span class="ms-2 badge" style="background:rgba(217,119,6,.1);color:#D97706;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $news->total() }} articles
        </span>
        @if(request()->hasAny(['search','country']))
            <span class="ms-1 badge" style="background:rgba(217,119,6,.1);color:#D97706;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
                <i class="bi bi-funnel-fill me-1"></i>Filtered
            </span>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Country</th>
                        <th>Headline</th>
                        <th>Source</th>
                        <th>Published</th>
                        <th style="width:80px;">Link</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $i => $item)
                        <tr>
                            <td style="color:var(--text-subtle);font-size:.72rem;font-weight:600;">
                                {{ $news->firstItem() + $i }}
                            </td>
                            <td style="white-space:nowrap;">
                                <div class="d-flex align-items-center gap-1.5">
                                    <x-country-flag :country="$item->country" size="sm" />
                                    <span class="badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:6px;padding:.25rem .5rem;">
                                        {{ $item->country->name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:.82rem;color:var(--text);line-height:1.35;">
                                    {{ \Illuminate\Support\Str::limit($item->title, 90) }}
                                </div>
                                @if($item->description)
                                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;line-height:1.4;">
                                        {{ \Illuminate\Support\Str::limit($item->description, 100) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->source)
                                    <span style="font-size:.72rem;font-weight:600;color:var(--text-muted);">
                                        <i class="bi bi-broadcast me-1"></i>{{ $item->source }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                <div style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">
                                    {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                                </div>
                                <div style="font-size:.68rem;color:var(--text-muted);">
                                    {{ \Carbon\Carbon::parse($item->published_at)->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                @if($item->url)
                                    <a href="{{ $item->url }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Open article"
                                       style="padding:4px 10px;">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <i class="bi bi-newspaper d-block mb-3" style="font-size:2.5rem;color:var(--text-subtle);opacity:.4;"></i>
                                <h6 style="color:var(--text-secondary);font-weight:600;">No news found</h6>
                                <p style="font-size:.82rem;color:var(--text-muted);max-width:280px;margin:0 auto 12px;">
                                    @if(request()->hasAny(['search','country']))
                                        No articles match your search. Try different keywords or country.
                                    @else
                                        No news articles have been indexed yet.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search','country']))
                                    <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Clear Filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($news->hasPages())
        <div class="card-body border-top" style="border-color:var(--border)!important;padding:14px 20px!important;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small style="color:var(--text-muted);font-size:.78rem;">
                    Showing {{ $news->firstItem() }}–{{ $news->lastItem() }}
                    of <strong>{{ $news->total() }}</strong> articles
                </small>
                {{ $news->withQueryString()->links() }}
            </div>
        </div>
    @endif

</div>

@endsection