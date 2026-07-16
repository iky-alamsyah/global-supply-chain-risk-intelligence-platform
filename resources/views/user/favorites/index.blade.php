@extends('layouts.dashboard')

@section('title', 'Favorites')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-star-fill me-2 text-warning" style="font-size:1.2rem;"></i>
            Favorite Watchlist
        </h1>
        <p class="page-header-sub mb-0">Curated list of key countries for fast access and close monitoring.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--success-bg); color:var(--success); border-left:4px solid var(--success)!important; border-radius:var(--radius-md);">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ── FILTER BAR ───────────────────────────────────────── --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-md-9">
            <label class="form-label">Search Favorites</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Search by country name, ISO, or region...">
                @if($search)
                    <a href="{{ route('favorites.index') }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);text-decoration:none;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
        </div>

    </div>
</form>

{{-- ── FAVORITES TABLE ───────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(245,158,11,.1);color:#fbbf24;">
            <i class="bi bi-star-fill"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Curated Countries</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $favorites->total() }} watchpoints
        </span>
        @if($search)
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
                        <th style="width:50px;">#</th>
                        <th>Country</th>
                        <th>Region</th>
                        <th>ISO3</th>
                        <th>Risk Level</th>
                        <th>Score</th>
                        <th>Notes</th>
                        <th>Added At</th>
                        <th style="width:200px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($favorites as $i => $fav)
                        @php
                            $country = $fav->country;
                            $risk = $country->riskScore;
                            $riskLevel = $risk?->risk_level ?? 'N/A';
                        @endphp
                        <tr>
                            <td style="color:var(--text-subtle);font-size:.72rem;font-weight:600;">
                                {{ $favorites->firstItem() + $i }}
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.85rem;color:var(--text);">
                                    {{ $country->flag ?? '🌍' }} {{ $country->name }}
                                </div>
                                <div style="font-size:.7rem;color:var(--text-muted);">
                                    {{ $country->official_name }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size:.78rem;color:var(--text-secondary);">{{ $country->region ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--surface-alt);color:var(--text-secondary);border:1px solid var(--border);font-weight:700;font-size:.68rem;border-radius:6px;">
                                    {{ $country->iso3 }}
                                </span>
                            </td>
                            <td>
                                @if($risk)
                                    @if($riskLevel == 'HIGH')
                                        <span class="badge badge-risk-high" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>HIGH
                                        </span>
                                    @elseif($riskLevel == 'MEDIUM')
                                        <span class="badge badge-risk-medium" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>MED
                                        </span>
                                    @else
                                        <span class="badge badge-risk-low" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>LOW
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-risk-na" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($risk)
                                    <span style="font-weight:700;font-family:monospace;font-size:.82rem;">
                                        {{ number_format($risk->risk_score, 1) }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:.78rem; font-style:italic;">
                                    {{ $fav->notes ?: 'No notes added' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:.75rem; color:var(--text-muted);">
                                    {{ $fav->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <a href="{{ route('countries.show', $country->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('favorites.toggle') }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="country_id" value="{{ $country->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Watchpoint">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center">
                                <div class="mb-3 d-inline-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; border-radius: 50%; background: rgba(245,158,11,.08); color: #fbbf24;">
                                    <i class="bi bi-star" style="font-size: 2rem;"></i>
                                </div>
                                <h6 style="color:var(--text-secondary);font-weight:600;">No favorites watchpoints</h6>
                                <p style="font-size:.82rem;color:var(--text-muted);max-width:320px;margin:0 auto 16px;">
                                    @if($search)
                                        No favorite watchpoints match your search criteria. Try a different query.
                                    @else
                                        Start adding key countries to your favorite watchlist for real-time monitoring and fast updates.
                                    @endif
                                </p>
                                @if(!$search)
                                    <a href="{{ route('countries.index') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-globe2 me-1"></i> Browse Global Countries
                                    </a>
                                @else
                                    <a href="{{ route('favorites.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-lg me-1"></i> Clear Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $favorites->links() }}
</div>

@endsection
