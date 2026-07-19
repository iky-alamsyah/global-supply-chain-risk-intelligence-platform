@extends('layouts.dashboard')

@section('title', 'Port Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-anchor me-2" style="color:#0891B2;font-size:1.2rem;"></i>
            Port Dashboard
        </h1>
        <p class="page-header-sub mb-0">Monitor global ports across the world.</p>
    </div>
</div>

{{-- ── WORLD PORT MAP ───────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
            <i class="bi bi-map-fill"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">World Port Map</span>
        <div class="ms-auto d-flex align-items-center gap-3" style="font-size:.72rem;font-weight:600;">
            <span class="d-flex align-items-center gap-1">
                <span style="width:10px;height:10px;border-radius:50%;background:#16A34A;display:inline-block;"></span>Active
            </span>
            <span class="d-flex align-items-center gap-1">
                <span style="width:10px;height:10px;border-radius:50%;background:#94A3B8;display:inline-block;"></span>Inactive
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="portWorldMap"></div>
    </div>
</div>

{{-- ── FILTER BAR ───────────────────────────────────────── --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Search Port</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Port name, city, code..."
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
                <a href="{{ route('ports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- ── PORT TABLE ───────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Port List</span>
        <span class="ms-2 badge" style="background:rgba(8,145,178,.1);color:#0891B2;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $ports->total() }} ports
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Port Name</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Code</th>
                        <th>Coordinates</th>
                        <th>Status</th>
                        <th style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $i => $port)
                        <tr>
                            <td style="color:var(--text-subtle);font-size:.72rem;font-weight:600;">
                                {{ $ports->firstItem() + $i }}
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.85rem;color:var(--text);">
                                    <i class="bi bi-anchor me-1" style="color:#0891B2;font-size:.7rem;"></i>
                                    {{ $port->port_name }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('countries.show', $port->country) }}"
                                   class="d-inline-flex align-items-center gap-1.5"
                                   style="font-size:.78rem;font-weight:600;color:var(--primary-light);text-decoration:none;">
                                    <x-country-flag :country="$port->country" size="sm" />
                                    <span>{{ $port->country->name }}</span>
                                </a>
                            </td>
                            <td style="font-size:.78rem;color:var(--text-secondary);">
                                {{ $port->city ?: '—' }}
                            </td>
                            <td>
                                @if($port->port_code)
                                    <span class="badge" style="background:var(--surface-alt);color:var(--text-secondary);border:1px solid var(--border);font-weight:700;font-size:.68rem;border-radius:6px;">
                                        {{ $port->port_code }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($port->latitude && $port->longitude)
                                    <span style="font-size:.72rem;font-family:monospace;color:var(--text-muted);">
                                        {{ number_format($port->latitude, 3) }},
                                        {{ number_format($port->longitude, 3) }}
                                    </span>
                                @else
                                    <span style="color:var(--text-subtle);">—</span>
                                @endif
                            </td>
                            <td>
                                @if(strtoupper($port->status ?? '') === 'ACTIVE')
                                    <span class="badge badge-status-active" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;border:1px solid rgba(22,163,74,.2);">
                                        <i class="bi bi-circle-fill me-1" style="font-size:.4rem;"></i>Active
                                    </span>
                                @else
                                    <span class="badge badge-status-inactive" style="border-radius:99px;font-size:.7rem;padding:.3rem .65rem;border:1px solid rgba(100,116,139,.2);">
                                        <i class="bi bi-circle me-1" style="font-size:.4rem;"></i>{{ $port->status ?? 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ports.show', $port) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center">
                                <i class="bi bi-anchor d-block mb-3" style="font-size:2.5rem;color:var(--text-subtle);opacity:.4;"></i>
                                <h6 style="color:var(--text-secondary);font-weight:600;">No ports found</h6>
                                <p style="font-size:.82rem;color:var(--text-muted);max-width:280px;margin:0 auto 12px;">
                                    @if(request()->hasAny(['search','country']))
                                        No ports match your search. Try different keywords or clear the filters.
                                    @else
                                        No ports registered in the system yet.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search','country']))
                                    <a href="{{ route('ports.index') }}" class="btn btn-outline-secondary btn-sm">
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

    @if($ports->hasPages())
        <div class="card-body border-top" style="border-color:var(--border)!important;padding:14px 20px!important;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small style="color:var(--text-muted);font-size:.78rem;">
                    Showing {{ $ports->firstItem() }}–{{ $ports->lastItem() }}
                    of <strong>{{ $ports->total() }}</strong> ports
                </small>
                {{ $ports->withQueryString()->links() }}
            </div>
        </div>
    @endif

</div>

<script>window.portsMap = @json($portsMap);</script>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("portWorldMap");
    if (!el) return;

    const map = L.map("portWorldMap").setView([20, 0], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap, © CartoDB',
        maxZoom: 18
    }).addTo(map);

    window.portsMap.forEach(port => {
        if (!port.lat || !port.lng) return;

        const isActive = (port.status ?? '').toUpperCase() === 'ACTIVE';

        L.circleMarker([port.lat, port.lng], {
            radius      : 5,
            color       : '#fff',
            weight      : 1.5,
            fillColor   : isActive ? '#16A34A' : '#94A3B8',
            fillOpacity : 0.9
        })
        .bindPopup(`
            <div style="font-family:Inter,sans-serif;min-width:160px;">
                <div style="font-weight:700;font-size:.85rem;margin-bottom:6px;">
                    ⚓ ${port.name}
                </div>
                <div style="font-size:.75rem;color:#64748B;line-height:1.7;">
                    <div><strong>Country:</strong> ${port.country}</div>
                    <div><strong>Code:</strong> ${port.code ?? '—'}</div>
                    <div><strong>Status:</strong>
                        <span style="color:${isActive ? '#16A34A' : '#94A3B8'};font-weight:700;">
                            ${port.status ?? 'N/A'}
                        </span>
                    </div>
                </div>
            </div>
        `)
        .addTo(map);
    });
});
</script>
@endpush

@endsection