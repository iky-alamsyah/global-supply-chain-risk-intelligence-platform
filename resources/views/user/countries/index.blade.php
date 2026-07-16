@extends('layouts.dashboard')

@section('title', 'Global Countries')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-globe-americas me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Global Countries
        </h1>
        <p class="page-header-sub mb-0">Monitor supply chain risk across {{ $countries->total() }} countries worldwide.</p>
    </div>
</div>

{{-- ── FILTER BAR ───────────────────────────────────────── --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-md-5">
            <label class="form-label">Search Country</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control"
                       placeholder="Country name, ISO code...">
                @if(request('search'))
                    <a href="{{ request()->url() }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Region --}}
        <div class="col-md-3">
            <label class="form-label">Region</label>
            <select name="region" class="form-select">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" @selected(request('region') == $region)>{{ $region }}</option>
                @endforeach
            </select>
        </div>

        {{-- Risk Level --}}
        <div class="col-md-2">
            <label class="form-label">Risk Level</label>
            <select name="risk" class="form-select">
                <option value="">All Levels</option>
                <option value="HIGH"   @selected(request('risk')=='HIGH')>🔴 High</option>
                <option value="MEDIUM" @selected(request('risk')=='MEDIUM')>🟡 Medium</option>
                <option value="LOW"    @selected(request('risk')=='LOW')>🟢 Low</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search','region','risk']))
                <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- ── COUNTRY TABLE ────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Country List</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $countries->total() }} total
        </span>
        @if(request()->hasAny(['search','region','risk']))
            <span class="ms-1 badge" style="background:rgba(217,119,6,.1);color:#D97706;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
                <i class="bi bi-funnel-fill me-1"></i>Filtered
            </span>
        @endif
    </div>

@php
    $favoriteCountryIds = auth()->user()->favorites()->pluck('country_id')->toArray();
@endphp

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="width: 40px;"></th>
                        <th>Country</th>
                        <th>Region</th>
                        <th>ISO</th>
                        <th>Risk Level</th>
                        <th>Score</th>
                        <th>Weather</th>
                        <th>Rate (USD)</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $i => $country)
                        @php
                            $isFavorited = in_array($country->id, $favoriteCountryIds);
                        @endphp
                        <tr>
                            <td style="color:var(--text-subtle);font-size:.72rem;font-weight:600;">
                                {{ $countries->firstItem() + $i }}
                            </td>
                            <td>
                                <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="country_id" value="{{ $country->id }}">
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none" style="border:none; background:none;" title="{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}">
                                        <i class="bi {{ $isFavorited ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" style="font-size: 1.05rem; cursor: pointer;"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.85rem;color:var(--text);">
                                    {{ $country->name }}
                                </div>
                                @if($country->capital)
                                    <div style="font-size:.7rem;color:var(--text-muted);">
                                        <i class="bi bi-building me-1"></i>{{ $country->capital }}
                                    </div>
                                @endif
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
                                @if($country->riskScore)
                                    @if($country->riskScore->risk_level == 'HIGH')
                                        <span class="badge badge-risk-high" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>HIGH
                                        </span>
                                    @elseif($country->riskScore->risk_level == 'MEDIUM')
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
                                @if($country->riskScore)
                                    <span style="font-weight:700;font-family:monospace;font-size:.82rem;">
                                        {{ number_format($country->riskScore->risk_score, 1) }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($country->weatherCaches->isNotEmpty())
                                    <span style="font-size:.82rem;font-weight:600;font-family:monospace;">
                                        {{ number_format($country->weatherCaches->last()->temperature, 1) }}°C
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($country->currencyCaches->isNotEmpty())
                                    <span style="font-size:.82rem;font-weight:600;font-family:monospace;">
                                        {{ number_format($country->currencyCaches->last()->exchange_rate, 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('countries.show', $country->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-5 text-center">
                                <i class="bi bi-globe2 d-block mb-3" style="font-size:2.5rem;color:var(--text-subtle);opacity:.4;"></i>
                                <h6 style="color:var(--text-secondary);font-weight:600;">No countries found</h6>
                                <p style="font-size:.82rem;color:var(--text-muted);max-width:280px;margin:0 auto 12px;">
                                    @if(request()->hasAny(['search','region','risk']))
                                        No countries match your current filters. Try adjusting your search criteria.
                                    @else
                                        No countries are registered in the system yet.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search','region','risk']))
                                    <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary btn-sm">
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

    @if($countries->hasPages())
        <div class="card-body border-top" style="border-color:var(--border)!important;padding:14px 20px!important;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small style="color:var(--text-muted);font-size:.78rem;">
                    Showing {{ $countries->firstItem() }}–{{ $countries->lastItem() }}
                    of <strong>{{ $countries->total() }}</strong> countries
                </small>
                {{ $countries->withQueryString()->links() }}
            </div>
        </div>
    @endif

</div>

@endsection