@extends('layouts.dashboard')

@section('title', $port->port_name . ' — Port Detail')

@section('content')

{{-- Hero Banner --}}
<div style="background:linear-gradient(135deg,#0F172A,#0891B2 80%);border-radius:var(--radius-lg);padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at top right,rgba(8,145,178,.3) 0%,transparent 60%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <a href="{{ route('ports.index') }}" style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;flex-shrink:0;font-size:1rem;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,.22)'" onmouseout="this.style.background='rgba(255,255,255,.12)'">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">⚓</div>
            </div>
            <h1 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-.02em;">{{ $port->port_name }}</h1>
            <p style="color:rgba(255,255,255,.65);font-size:.85rem;margin:0;">
                <i class="bi bi-globe-americas me-1"></i>
                <a href="{{ route('countries.show', $port->country) }}" style="color:rgba(255,255,255,.75);text-decoration:none;font-weight:600;">{{ $port->country->name }}</a>
                @if($port->city) · <i class="bi bi-pin-map me-1"></i>{{ $port->city }} @endif
            </p>
        </div>
        <div>
            @if(strtoupper($port->status ?? '') === 'ACTIVE')
                <span style="background:rgba(22,163,74,.2);color:#4ade80;border:1px solid rgba(74,222,128,.3);padding:.4rem 1rem;border-radius:99px;font-size:.78rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;"></span>ACTIVE
                </span>
            @else
                <span style="background:rgba(148,163,184,.15);color:#94A3B8;border:1px solid rgba(148,163,184,.25);padding:.4rem 1rem;border-radius:99px;font-size:.78rem;font-weight:700;">
                    {{ $port->status ?? 'INACTIVE' }}
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--c:#0891B2;background:rgba(8,145,178,.06);border-color:rgba(8,145,178,.2);">
            <div class="stat-icon" style="background:rgba(8,145,178,.12);color:#0891B2;"><i class="bi bi-upc-scan"></i></div>
            <div class="stat-content"><small>UN/LOCODE</small><h3 style="font-size:1.2rem;color:#0891B2;">{{ $port->port_code ?: '—' }}</h3></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:rgba(30,58,138,.06);border-color:rgba(30,58,138,.2);">
            <div class="stat-icon" style="background:rgba(30,58,138,.12);color:#2563EB;"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="stat-content">
                <small>Latitude</small>
                <h3 style="font-size:1.2rem;color:#2563EB;font-family:monospace;">{{ $port->latitude ? number_format($port->latitude, 4) : '—' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:rgba(30,58,138,.06);border-color:rgba(30,58,138,.2);">
            <div class="stat-icon" style="background:rgba(30,58,138,.12);color:#2563EB;"><i class="bi bi-geo-fill"></i></div>
            <div class="stat-content">
                <small>Longitude</small>
                <h3 style="font-size:1.2rem;color:#2563EB;font-family:monospace;">{{ $port->longitude ? number_format($port->longitude, 4) : '—' }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:rgba(22,163,74,.06);border-color:rgba(22,163,74,.2);">
            <div class="stat-icon" style="background:rgba(22,163,74,.12);color:#16A34A;"><i class="bi bi-clock"></i></div>
            <div class="stat-content"><small>Timezone</small><h3 style="font-size:1.1rem;color:#16A34A;">{{ $port->timezone ?: '—' }}</h3></div>
        </div>
    </div>
</div>

{{-- Info + Map --}}
<div class="row g-3 mb-4">

    {{-- Info Panel --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Port Information</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-enterprise mb-0">
                    <tbody>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;width:40%;white-space:nowrap;">Port Name</td>
                            <td style="font-weight:600;">{{ $port->port_name }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">UN/LOCODE</td>
                            <td>
                                <span class="badge" style="background:var(--surface-alt);color:var(--text-secondary);border:1px solid var(--border);font-weight:700;font-size:.75rem;">
                                    {{ $port->port_code ?: '—' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">Country</td>
                            <td>
                                <a href="{{ route('countries.show', $port->country) }}" style="color:var(--primary-light);font-weight:600;text-decoration:none;">
                                    {{ $port->country->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">City</td>
                            <td style="font-weight:600;">{{ $port->city ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">Status</td>
                            <td>
                                @if(strtoupper($port->status ?? '') === 'ACTIVE')
                                    <span class="badge badge-status-active" style="border-radius:99px;font-size:.7rem;">Active</span>
                                @else
                                    <span class="badge badge-status-inactive" style="border-radius:99px;font-size:.7rem;">{{ $port->status ?? 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">Latitude</td>
                            <td style="font-family:monospace;font-weight:600;">{{ $port->latitude ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">Longitude</td>
                            <td style="font-family:monospace;font-weight:600;">{{ $port->longitude ?? '—' }}</td>
                        </tr>
                        @if($port->timezone)
                        <tr>
                            <td style="color:var(--text-muted);font-size:.75rem;font-weight:600;">Timezone</td>
                            <td style="font-weight:600;">{{ $port->timezone }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
                    <i class="bi bi-map-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Port Location</span>
            </div>
            <div class="card-body p-0">
                <div id="portMap" style="height:400px;border-radius:0 0 var(--radius-lg) var(--radius-lg);"></div>
            </div>
        </div>
    </div>

</div>

{{-- Description --}}
@if($port->description)
<div class="card mb-4">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-file-text-fill"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Description</span>
    </div>
    <div class="card-body">
        <p style="font-size:.88rem;line-height:1.7;color:var(--text-secondary);margin:0;">{{ $port->description }}</p>
    </div>
</div>
@endif

<script>
window.portData = {
    lat  : {{ $port->latitude ?? 0 }},
    lng  : {{ $port->longitude ?? 0 }},
    name : @json($port->port_name),
    code : @json($port->port_code ?? ''),
    city : @json($port->city ?? ''),
    status: @json($port->status ?? '')
};
</script>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (!document.getElementById("portMap")) return;

    const p   = window.portData;
    const map = L.map("portMap").setView([p.lat, p.lng], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap, © CartoDB', maxZoom: 18
    }).addTo(map);

    const icon = L.divIcon({
        html      : `<div style="width:40px;height:40px;background:linear-gradient(135deg,#0891B2,#0F172A);border-radius:50% 50% 50% 0;transform:rotate(-45deg);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(8,145,178,.4);border:3px solid #fff;">
                        <span style="transform:rotate(45deg);font-size:1rem;">⚓</span>
                     </div>`,
        iconSize  : [40, 40],
        iconAnchor: [20, 40],
        className : ''
    });

    L.marker([p.lat, p.lng], { icon })
        .addTo(map)
        .bindPopup(`
            <div style="font-family:Inter,sans-serif;min-width:180px;">
                <div style="font-weight:700;font-size:.9rem;margin-bottom:6px;">⚓ ${p.name}</div>
                <table style="width:100%;font-size:.78rem;border-collapse:collapse;">
                    <tr><td style="color:#64748B;padding:.1rem 0;">Code</td><td style="font-weight:600;">${p.code || '—'}</td></tr>
                    <tr><td style="color:#64748B;padding:.1rem 0;">City</td><td style="font-weight:600;">${p.city || '—'}</td></tr>
                    <tr><td style="color:#64748B;padding:.1rem 0;">Status</td><td style="font-weight:600;color:${p.status?.toUpperCase()==='ACTIVE'?'#16A34A':'#94A3B8'};">${p.status || 'N/A'}</td></tr>
                </table>
            </div>
        `)
        .openPopup();
});
</script>
@endpush

@endsection