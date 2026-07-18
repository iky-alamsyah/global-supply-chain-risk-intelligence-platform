@extends('layouts.admin')

@section('title', 'Port Detail — ' . $port->port_name)

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary btn-sm p-1.5" style="border-radius:var(--radius-sm);">
                <i class="bi bi-arrow-left" style="font-size:1rem; line-height:1;"></i>
            </a>
            <div>
                <h1 class="page-header-title">{{ $port->port_name }}</h1>
                <p class="page-header-sub mb-0">LOCODE: {{ $port->port_code ?: '—' }} · {{ $port->city ?: 'Unknown City' }}, {{ $port->country ? $port->country->name : 'Unknown Country' }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil-fill me-1"></i> Edit Port
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left Side: Details Card --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle me-1"></i> Port Specifications</h5>
                @if(strtoupper(trim($port->status)) === 'ACTIVE')
                    <span class="badge bg-success" style="font-size:.75rem; padding:.3rem .6rem; border-radius:99px;">Active</span>
                @else
                    <span class="badge bg-secondary" style="font-size:.75rem; padding:.3rem .6rem; border-radius:99px;">Inactive</span>
                @endif
            </div>

            <div class="card-body p-4">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2" style="width: 160px;">Port Name</td>
                            <td class="text-dark fw-bold py-2">{{ $port->port_name }}</td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">UN/LOCODE</td>
                            <td class="text-dark fw-bold py-2" style="font-family: monospace;">{{ $port->port_code ?: '—' }}</td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">Country</td>
                            <td class="text-dark py-2">
                                @if($port->country)
                                    <span class="me-1">{{ $port->country->flag }}</span>
                                    <strong>{{ $port->country->name }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">City</td>
                            <td class="text-dark py-2"><strong>{{ $port->city ?: '—' }}</strong></td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">Timezone</td>
                            <td class="text-dark py-2"><strong>{{ $port->timezone ?: '—' }}</strong></td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">Latitude</td>
                            <td class="text-dark py-2" style="font-family: monospace;">{{ $port->latitude }}</td>
                        </tr>
                        <tr class="border-bottom py-2">
                            <td class="text-muted fw-medium py-2">Longitude</td>
                            <td class="text-dark py-2" style="font-family: monospace;">{{ $port->longitude }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-medium py-2">Last Updated</td>
                            <td class="text-muted py-2" style="font-size: .85rem;">{{ $port->updated_at->diffForHumans() }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($port->description)
                    <div class="mt-4">
                        <label class="form-label fw-bold text-dark">Description / Operational Notes</label>
                        <div class="p-3 bg-light border" style="border-radius: var(--radius-sm); font-size: .85rem; line-height: 1.5; color: var(--text-secondary);">
                            {{ $port->description }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Side: Location Map --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt-fill me-1"></i> Geographic Location</h5>
            </div>
            <div class="card-body p-0">
                <div id="portMap" style="height: 400px; border-radius: 0 0 var(--radius-md) var(--radius-md);"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lat = {{ $port->latitude }};
    const lng = {{ $port->longitude }};
    const portName = "{{ addslashes($port->port_name) }}";
    const status = "{{ $port->status }}";

    const map = L.map('portMap').setView([lat, lng], 10);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors © CartoDB',
        maxZoom: 19
    }).addTo(map);

    const isActive = status.toUpperCase() === 'ACTIVE';

    L.circleMarker([lat, lng], {
        radius: 10,
        color: '#fff',
        weight: 3,
        fillColor: isActive ? '#10b981' : '#6b7280',
        fillOpacity: 0.9
    })
    .addTo(map)
    .bindPopup(`
        <div style="font-family:Inter,sans-serif; min-width:150px;">
            <h6 style="font-weight:700; margin-bottom:.3rem; color:var(--text);">🚢 ${portName}</h6>
            <span style="font-size:.7rem; background:${isActive?'#10b98122':'#6b728022'}; color:${isActive?'#10b981':'#6b7280'}; padding:.1rem .4rem; border-radius:99px; font-weight:700;">
                ${status.toUpperCase()}
            </span>
        </div>
    `)
    .openPopup();
});
</script>
@endpush
