@extends('layouts.admin')

@section('title', 'Country Management')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-globe-americas me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Country Management
        </h1>
        <p class="page-header-sub mb-0">Manage global trade countries, coordinates, and view aggregated risk metrics.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.countries.export', request()->all()) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1.5">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
        <a href="{{ route('admin.countries.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> Add Country
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

{{-- Filter & Sort Bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-md-4">
            <label class="form-label">Search Country</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Name, ISO code, capital...">
            </div>
        </div>

        {{-- Region Filter --}}
        <div class="col-md-2">
            <label class="form-label">Region</label>
            <select name="region" class="form-select">
                <option value="">All Regions</option>
                @foreach($regions as $r)
                    <option value="{{ $r }}" @selected($regionFilter == $r)>{{ $r }}</option>
                @endforeach
            </select>
        </div>

        {{-- Sort By --}}
        <div class="col-md-2">
            <label class="form-label">Sort By</label>
            <select name="sort" class="form-select">
                <option value="name" @selected($sort == 'name')>Country Name</option>
                <option value="risk_score" @selected($sort == 'risk_score')>Risk Score</option>
            </select>
        </div>

        {{-- Direction --}}
        <div class="col-md-2">
            <label class="form-label">Direction</label>
            <select name="direction" class="form-select">
                <option value="asc" @selected($direction == 'asc')>Ascending</option>
                <option value="desc" @selected($direction == 'desc')>Descending</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if($search || $regionFilter || $sort != 'name' || $direction != 'asc')
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Country Registry</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $countries->total() }} records
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">Flag</th>
                        <th>Country Name</th>
                        <th>Official Name</th>
                        <th>ISO2</th>
                        <th>ISO3</th>
                        <th>Region</th>
                        <th>Capital</th>
                        <th>Population</th>
                        <th>GDP (USD)</th>
                        <th>Risk Score</th>
                        <th>Ports</th>
                        <th>Last Updated</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $c)
                        @php
                            $latestStat = $c->statistics()->latest('year')->first();
                            $gdp = $latestStat ? $latestStat->gdp : null;
                            
                            $risk = $c->riskScore;
                            $riskLevel = $risk?->risk_level ?? 'N/A';
                            $riskColor = match($riskLevel) {
                                'HIGH'   => 'bg-danger',
                                'MEDIUM' => 'bg-warning text-dark',
                                'LOW'    => 'bg-success',
                                default  => 'bg-secondary',
                            };
                            
                            $weather = $c->weatherCaches()->orderByDesc('created_at')->first();
                        @endphp
                        <tr>
                            <td>
                                <span style="font-size: 1.25rem;">{{ $c->flag ?? '🌍' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.countries.show', $c) }}" style="font-weight:700; color:var(--text); text-decoration:none;" class="hover-link">
                                    {{ $c->name }}
                                </a>
                            </td>
                            <td>
                                <span style="font-size:.8rem; color:var(--text-secondary);">{{ \Illuminate\Support\Str::limit($c->official_name ?? '—', 24) }}</span>
                            </td>
                            <td style="font-family: monospace;">{{ $c->iso2 }}</td>
                            <td style="font-family: monospace;">{{ $c->iso3 }}</td>
                            <td>{{ $c->region }}</td>
                            <td>{{ $c->capital ?? '—' }}</td>
                            <td>{{ $c->population ? number_format($c->population) : '—' }}</td>
                            <td>
                                @if($gdp)
                                    ${{ number_format((float)$gdp / 1000000000, 1) }}B
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($risk)
                                    <span class="badge {{ $riskColor }}" style="font-size:.7rem; padding:.25rem .55rem; border-radius:99px;">
                                        {{ number_format($risk->risk_score, 1) }} ({{ $riskLevel }})
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Unrated</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $c->ports->count() }}</span>
                            </td>
                            <td>
                                <span style="font-size:.7rem; color:var(--text-subtle);">
                                    {{ $weather ? $weather->updated_at->diffForHumans() : '—' }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <a href="{{ route('admin.countries.show', $c) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.countries.edit', $c) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCountryModal"
                                            data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-5 text-center text-muted">
                                <i class="bi bi-globe-americas d-block mb-2" style="font-size: 2rem; opacity: .3;"></i>
                                <strong>No countries found matching your filters.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $countries->links() }}
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteCountryModal" tabindex="-1" aria-labelledby="deleteCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="deleteCountryModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center text-danger" 
                     style="width: 54px; height: 54px; border-radius: 50%; background: rgba(220,38,38,.08);">
                    <i class="bi bi-trash" style="font-size: 1.6rem;"></i>
                </div>
                <p class="mb-0 text-secondary" style="font-size: .9rem;">
                    Are you sure you want to delete <strong id="delete_country_name" class="text-dark"></strong> from GSCRIP database?
                </p>
                <p class="text-muted mt-1 mb-0" style="font-size: .78rem;">
                    All associated historical risk scores, weather alerts, and ports mapping will be permanently deleted.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCountryForm" method="POST" class="flex-fill m-0">
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
    const deleteForm = document.getElementById("deleteCountryForm");
    const deleteName = document.getElementById("delete_country_name");

    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");

            deleteForm.action = `/admin/countries/${id}`;
            deleteName.textContent = name;
        });
    });
});
</script>
@endpush
