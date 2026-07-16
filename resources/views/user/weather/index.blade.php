@extends('layouts.dashboard')

@section('title', 'Weather Intelligence')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-cloud-sun me-2" style="color:#0891B2;font-size:1.2rem;"></i>
            Weather Intelligence
        </h1>
        <p class="page-header-sub mb-0">
            Real-time weather monitoring and critical logistics alerts for active trade routes.
        </p>
    </div>
    
    <div class="d-flex gap-2">
        <form action="{{ route('weather.refresh') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-clockwise"></i> Refresh All Weather
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--success-bg); color:var(--success); border-left:4px solid var(--success)!important; border-radius:var(--radius-md);">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Filter Bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-6">
            <label class="form-label fw-semibold">Search Country</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Country name, code, capital..."
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ request()->url() }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Filter by Region</label>
            <select name="region" class="form-select">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" @selected(request('region') == $region)>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search','region']))
                <a href="{{ route('weather.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Weather Cards / Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
            <i class="bi bi-cloud-sun"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Active Weather Feeds</span>
        <span class="ms-2 badge" style="background:rgba(8,145,178,.1);color:#0891B2;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $countries->total() }} countries
        </span>
        @if(request()->hasAny(['search','region']))
            <span class="ms-1 badge" style="background:rgba(8,145,178,.1);color:#0891B2;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
                <i class="bi bi-funnel-fill me-1"></i>Filtered
            </span>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Condition</th>
                        <th>Temp</th>
                        <th>Wind & Rain</th>
                        <th>Atmosphere</th>
                        <th>Weather Alerts</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        @php
                            $weather = $country->latestWeather;
                            $alerts = $country->weatherAlerts;
                            
                            $main = strtolower($weather->weather_main ?? '');
                            $icon = 'bi-cloud-sun';
                            $iconColor = '#3b82f6';
                            
                            if (str_contains($main, 'thunderstorm')) {
                                $icon = 'bi-cloud-lightning-rain-fill';
                                $iconColor = '#ef4444';
                            } elseif (str_contains($main, 'rain') || str_contains($main, 'drizzle')) {
                                $icon = 'bi-cloud-rain-heavy-fill';
                                $iconColor = '#06b6d4';
                            } elseif (str_contains($main, 'snow')) {
                                $icon = 'bi-cloud-snow-fill';
                                $iconColor = '#a855f7';
                            } elseif (str_contains($main, 'fog') || str_contains($main, 'mist')) {
                                $icon = 'bi-cloud-fog2-fill';
                                $iconColor = '#64748b';
                            } elseif (str_contains($main, 'overcast') || str_contains($main, 'cloudy')) {
                                $icon = 'bi-cloud-fill';
                                $iconColor = '#94a3b8';
                            } elseif (str_contains($main, 'clear') || str_contains($main, 'sun')) {
                                $icon = 'bi-sun-fill';
                                $iconColor = '#eab308';
                            }
                        @endphp
                        <tr>
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="font-size:1.1rem;">🌍</div>
                                    <div>
                                        <a href="{{ route('countries.show', $country) }}" style="font-weight:700;color:var(--text);text-decoration:none;" class="hover-link">
                                            {{ $country->name }}
                                        </a>
                                        <div style="font-size:.7rem;color:var(--text-subtle);">{{ $country->region }} · {{ $country->iso3 }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align:middle;">
                                @if($weather)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $icon }}" style="font-size:1.1rem;color:{{ $iconColor }};"></i>
                                        <div>
                                            <span style="font-weight:600;color:var(--text);">{{ $weather->weather_main ?? '—' }}</span>
                                            <div style="font-size:.7rem;color:var(--text-subtle);" title="{{ $weather->weather_description }}">
                                                {{ \Illuminate\Support\Str::limit($weather->weather_description ?? '—', 28) }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">—</span>
                                @endif
                            </td>
                            <td style="font-weight:700;font-size:.85rem;font-family:monospace;vertical-align:middle;">
                                @if($weather && $weather->temperature !== null)
                                    {{ number_format($weather->temperature, 1) }}°C
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;font-size:.78rem;">
                                @if($weather)
                                    <div><i class="bi bi-wind me-1 text-subtle"></i>{{ $weather->wind_speed !== null ? number_format($weather->wind_speed, 1) . ' km/h' : '—' }}</div>
                                    <div class="mt-0.5"><i class="bi bi-cloud-drizzle me-1 text-subtle"></i>{{ $weather->rainfall !== null ? number_format($weather->rainfall, 1) . ' mm' : '—' }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;font-size:.78rem;">
                                @if($weather)
                                    <div><i class="bi bi-droplet me-1 text-subtle"></i>Humidity: {{ $weather->humidity !== null ? $weather->humidity . '%' : '—' }}</div>
                                    <div class="mt-0.5"><i class="bi bi-speedometer2 me-1 text-subtle"></i>Pressure: {{ $weather->pressure !== null ? number_format($weather->pressure, 0) . ' hPa' : '—' }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                @forelse($alerts as $alert)
                                    <span class="badge mb-1 d-inline-block" 
                                          style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;
                                                 @if(strtoupper($alert->severity) === 'CRITICAL') background:#7F1D1D;color:#FEE2E2;
                                                 @elseif(strtoupper($alert->severity) === 'HIGH') background:var(--danger-bg);color:var(--danger);
                                                 @elseif(strtoupper($alert->severity) === 'MEDIUM') background:var(--warning-bg);color:var(--warning);
                                                 @else background:var(--success-bg);color:var(--success);@endif"
                                          title="{{ $alert->description }}">
                                        {{ $alert->title }}
                                    </span>
                                @empty
                                    <span class="badge bg-light text-muted border" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">No Risk</span>
                                @endforelse
                            </td>
                            <td style="font-size:.72rem;color:var(--text-subtle);vertical-align:middle;">
                                @if($weather && $weather->weather_time)
                                    {{ \Carbon\Carbon::parse($weather->weather_time)->diffForHumans() }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted" style="font-size:.82rem;">
                                <i class="bi bi-cloud-slash d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                <strong>No significant weather risks detected.</strong>
                                <p class="mb-0 text-subtle" style="font-size:.72rem;">Try modifying search parameters or refresh the weather data feed.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination Links --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $countries->links('pagination::bootstrap-5') }}
</div>

@endsection