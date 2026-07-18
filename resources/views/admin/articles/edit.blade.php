@extends('layouts.admin')

@section('title', 'Edit Article')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm p-1.5" style="border-radius:var(--radius-sm);">
            <i class="bi bi-arrow-left" style="font-size:1rem; line-height:1;"></i>
        </a>
        <div>
            <h1 class="page-header-title">Edit Article: {{ $article->title }}</h1>
            <p class="page-header-sub mb-0">Modify analysis reports, trade insights, or logistics alerts for the platform feed.</p>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--danger-bg); color:var(--danger); border-left:4px solid var(--danger)!important; border-radius:var(--radius-md);">
        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Please fix the following errors:</h6>
        <ul class="mb-0 ps-3" style="font-size: .82rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Form Card --}}
<div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
    <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-1"></i> Update Registry Specifications</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Title --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" class="form-control form-control-lg" required>
                </div>

                {{-- Category --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="economy" @selected(old('category', $article->category) == 'economy')>Economy</option>
                        <option value="trade" @selected(old('category', $article->category) == 'trade')>Trade</option>
                        <option value="shipping" @selected(old('category', $article->category) == 'shipping')>Shipping</option>
                        <option value="logistics" @selected(old('category', $article->category) == 'logistics')>Logistics</option>
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" @selected(old('status', $article->status) == 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $article->status) == 'published')>Published</option>
                        <option value="archived" @selected(old('status', $article->status) == 'archived')>Archived</option>
                    </select>
                </div>

                {{-- Country association --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Associate Country</label>
                    <select name="country_id" class="form-select">
                        <option value="">No Country Association</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" @selected(old('country_id', $article->country_id) == $c->id)>
                                {{ $c->flag }} {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Thumbnail --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Thumbnail Image</label>
                    @if($article->thumbnail)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="Current Thumbnail" class="rounded border" style="max-height: 120px;">
                        </div>
                    @endif
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <span class="text-muted" style="font-size: .72rem;">Supported formats: JPG, PNG, GIF. Max file size: 2MB. Leave blank to keep existing.</span>
                </div>

                {{-- Summary --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Summary / Excerpt</label>
                    <textarea name="summary" class="form-control" rows="3">{{ old('summary', $article->summary) }}</textarea>
                </div>

                {{-- Content --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Konten Artikel <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" required style="font-family: var(--font-sans); line-height: 1.6;">{{ old('content', $article->content) }}</textarea>
                </div>
            </div>

            <div class="mt-4 pt-2 text-end">
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection
