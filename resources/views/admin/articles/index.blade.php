@extends('layouts.admin')

@section('title', 'Artikel Analisis')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-file-earmark-text-fill me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Artikel Analisis
        </h1>
        <p class="page-header-sub mb-0">Publish custom supply chain risk intelligence, logistics reports, and global trade analyses.</p>
    </div>
    <div>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> Add Article
        </a>
    </div>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--success-bg); color:var(--success); border-left:4px solid var(--success)!important; border-radius:var(--radius-md);">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Dashboard Kecil (Stats Cards) --}}
<div class="row g-3 mb-4">
    {{-- Total --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59,130,246,.1);">
                <i class="bi bi-file-earmark-richtext" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Total Artikel</div>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">{{ number_format($totalArticles) }}</h4>
            </div>
        </div>
    </div>

    {{-- Draft --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-warning" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245,158,11,.1);">
                <i class="bi bi-pencil-square" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Draft</div>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">{{ number_format($draftCount) }}</h4>
            </div>
        </div>
    </div>

    {{-- Published --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,.1);">
                <i class="bi bi-globe" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Published</div>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">{{ number_format($publishedCount) }}</h4>
            </div>
        </div>
    </div>

    {{-- Archived --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-secondary" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(107,114,128,.1);">
                <i class="bi bi-archive" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Archived</div>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">{{ number_format($archivedCount) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Filters bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search Input --}}
        <div class="col-md-5">
            <label class="form-label">Search Articles</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Title, summary, keywords...">
            </div>
        </div>

        {{-- Category Filter --}}
        <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="economy" @selected($categoryFilter == 'economy')>Economy</option>
                <option value="trade" @selected($categoryFilter == 'trade')>Trade</option>
                <option value="shipping" @selected($categoryFilter == 'shipping')>Shipping</option>
                <option value="logistics" @selected($categoryFilter == 'logistics')>Logistics</option>
            </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="draft" @selected($statusFilter == 'draft')>Draft</option>
                <option value="published" @selected($statusFilter == 'published')>Published</option>
                <option value="archived" @selected($statusFilter == 'archived')>Archived</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if($search || $statusFilter || $categoryFilter)
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Table Card --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Articles Index Registry</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $articles->total() }} records
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">Thumbnail</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Tanggal Publish</th>
                        <th style="width:160px; text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $item)
                        @php
                            $statusColor = match($item->status) {
                                'published' => 'bg-success',
                                'archived'  => 'bg-secondary',
                                default     => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td>
                                @if($item->thumbnail)
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" 
                                         alt="{{ $item->title }}"
                                         class="rounded"
                                         style="width: 60px; height: 40px; object-fit: cover; border: 1px solid var(--border);">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" 
                                         style="width: 60px; height: 40px; font-size: 1.1rem;">
                                        📰
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                @if($item->summary)
                                    <div class="text-muted" style="font-size: .72rem; line-height: 1.3;">
                                        {{ \Illuminate\Support\Str::limit($item->summary, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-capitalize">{{ $item->category }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $statusColor }} text-capitalize" style="font-size: .68rem; padding: .25rem .55rem; border-radius: 99px;">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <span class="small font-semibold">{{ $item->author ? $item->author->name : 'Unknown' }}</span>
                            </td>
                            <td>
                                @if($item->published_at)
                                    <span class="small">{{ $item->published_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <a href="{{ route('articles.public.show', $item->slug) }}" class="btn btn-sm btn-outline-info" title="Preview Article" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteArticleModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->title }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="bi bi-file-earmark-richtext d-block mb-2" style="font-size: 2rem; opacity: .3;"></i>
                                <strong>No articles found matching filters.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $articles->links() }}
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-labelledby="deleteArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="deleteArticleModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center text-danger" 
                     style="width: 54px; height: 54px; border-radius: 50%; background: rgba(220,38,38,.08);">
                    <i class="bi bi-trash" style="font-size: 1.6rem;"></i>
                </div>
                <p class="mb-0 text-secondary" style="font-size: .9rem;">
                    Are you sure you want to delete <strong id="delete_article_name" class="text-dark"></strong>?
                </p>
                <p class="text-muted mt-1 mb-0" style="font-size: .78rem;">
                    This article will also be permanently removed from User News feed.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteArticleForm" method="POST" class="flex-fill m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteBtns = document.querySelectorAll(".delete-btn");
    const deleteForm = document.getElementById("deleteArticleForm");
    const deleteName = document.getElementById("delete_article_name");

    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");

            deleteForm.action = `/admin/articles/${id}`;
            deleteName.textContent = name;
        });
    });
});
</script>
@endpush
