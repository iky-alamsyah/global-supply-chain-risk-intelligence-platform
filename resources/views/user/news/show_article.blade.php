@extends('layouts.dashboard')

@section('title', $article->title)

@section('content')

{{-- Back Button --}}
<div class="mb-4">
    <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to News Feed
    </a>
</div>

{{-- Main Article Card --}}
<div class="card border-0 shadow-sm" style="border-radius: var(--radius-lg); background: var(--surface); overflow: hidden;">
    
    {{-- Thumbnail --}}
    @if($article->thumbnail)
        <div style="width: 100%; max-height: 400px; overflow: hidden; border-bottom: 1px solid var(--border);">
            <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                 alt="{{ $article->title }}" 
                 style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @endif

    <div class="card-body p-4 p-md-5">
        
        {{-- Metadata Row --}}
        <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
            <span class="badge bg-primary text-uppercase" style="font-size: .7rem; padding: .3rem .7rem; border-radius: 99px;">
                {{ $article->category }}
            </span>
            @if($article->country)
                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1" style="font-size: .7rem; padding: .3rem .7rem; border-radius: 99px;">
                    <span>{{ $article->country->flag }}</span>
                    <span>{{ $article->country->name }}</span>
                </span>
            @endif
            <span class="text-muted small ms-md-2">
                <i class="bi bi-clock me-1"></i> {{ $article->published_at ? $article->published_at->format('d M Y · H:i') : 'Draft' }}
            </span>
            <span class="text-muted small ms-md-2">
                <i class="bi bi-person me-1"></i> By {{ $article->author ? $article->author->name : 'Administrator' }}
            </span>
        </div>

        {{-- Headline --}}
        <h1 class="fw-bold text-dark mb-4" style="font-size: 2rem; line-height: 1.25; letter-spacing: -.02em;">
            {{ $article->title }}
        </h1>

        {{-- Summary block --}}
        @if($article->summary)
            <div class="p-4 mb-4" style="background: var(--surface-alt); border-left: 4px solid var(--primary-light); border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                <p class="mb-0 fw-semibold text-secondary" style="font-size: .95rem; line-height: 1.6; font-style: italic;">
                    {{ $article->summary }}
                </p>
            </div>
        @endif

        {{-- Main body text content --}}
        <div class="article-content text-dark" style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary);">
            {!! nl2br(e($article->content)) !!}
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.article-content p {
    margin-bottom: 1.5rem;
}
.article-content {
    font-family: 'Inter', sans-serif;
    color: #334155 !important;
}
</style>
@endpush
