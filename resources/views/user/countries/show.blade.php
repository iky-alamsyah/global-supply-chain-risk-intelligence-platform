@extends('layouts.dashboard')

@section('title', $country->name . ' — Country Detail')

@section('content')

@php
    $weather  = $country->weatherCaches->sortByDesc('created_at')->first();
    $currency = $country->currencyCaches->sortByDesc('created_at')->first();
    $risk     = $country->riskScore;

    $totalPorts    = $country->ports->count();
    $activePorts   = $country->ports->filter(fn($p) => strtoupper(trim($p->status)) === 'ACTIVE')->count();
    $inactivePorts = $totalPorts - $activePorts;

    $riskLevel = $risk?->risk_level ?? 'N/A';
    $riskScore = $risk ? number_format($risk->risk_score, 1) : '—';
    $riskColor = match($riskLevel) {
        'HIGH'   => '#ef4444',
        'MEDIUM' => '#f59e0b',
        'LOW'    => '#10b981',
        default  => '#6b7280',
    };
    $riskGradient = match($riskLevel) {
        'HIGH'   => 'linear-gradient(135deg,#ef444422,#ef444408)',
        'MEDIUM' => 'linear-gradient(135deg,#f59e0b22,#f59e0b08)',
        'LOW'    => 'linear-gradient(135deg,#10b98122,#10b98108)',
        default  => 'linear-gradient(135deg,#6b728022,#6b728008)',
    };

    $weatherIcon = match($weather?->weather_main ?? '') {
        'Clear'       => '☀️',
        'Clouds'      => '☁️',
        'Rain'        => '🌧️',
        'Drizzle'     => '🌦️',
        'Thunderstorm'=> '⛈️',
        'Snow'        => '❄️',
        'Mist','Fog'  => '🌫️',
        default       => '🌡️',
    };
@endphp

{{-- ================================================================ --}}
{{-- HERO BANNER                                                       --}}
{{-- ================================================================ --}}

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--success-bg); color:var(--success); border-left:4px solid var(--success)!important; border-radius:var(--radius-md);">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--danger-bg); color:var(--danger); border-left:4px solid var(--danger)!important; border-radius:var(--radius-md);">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="country-hero mb-4">
    <div class="country-hero-inner">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('countries.index') }}" class="hero-back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="country-flag-circle">
                    🌍
                </div>
                <div>
                    <h1 class="hero-title mb-0">{{ $country->name }}</h1>
                    <p class="hero-sub mb-0">{{ $country->official_name }}</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('favorites.toggle') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="country_id" value="{{ $country->id }}">
                    @php
                        $isFavorited = auth()->user()->favorites()->where('country_id', $country->id)->exists();
                    @endphp
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-1.5" style="border-radius:var(--radius-sm); font-weight:600; font-size:.8rem; padding:6px 12px; border:1px solid {{ $isFavorited ? 'rgba(245,158,11,.4)' : 'rgba(255,255,255,.2)' }}; background:{{ $isFavorited ? 'rgba(245,158,11,.15)' : 'rgba(255,255,255,.1)' }}; color:{{ $isFavorited ? '#fbbf24' : '#fff' }}; transition:all .2s;">
                        <i class="bi {{ $isFavorited ? 'bi-star-fill text-warning' : 'bi-star' }}"></i> {{ $isFavorited ? 'Favorited' : 'Add Favorite' }}
                    </button>
                </form>

                <form action="{{ route('countries.refresh', $country) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-1" style="border-radius:var(--radius-sm); font-weight:600; font-size:.8rem; padding:6px 12px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.1); color:#fff; transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Data
                    </button>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <span class="hero-badge"><i class="bi bi-globe2 me-1"></i>{{ $country->iso3 }}</span>
            <span class="hero-badge"><i class="bi bi-map me-1"></i>{{ $country->region }}</span>
            <span class="hero-badge"><i class="bi bi-building me-1"></i>{{ $country->capital }}</span>
            <span class="hero-badge"><i class="bi bi-people me-1"></i>{{ number_format($country->population) }}</span>
            @if($country->currency_code)
                <span class="hero-badge"><i class="bi bi-currency-exchange me-1"></i>{{ $country->currency_code }}</span>
            @endif
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- STAT CARDS ROW                                                    --}}
{{-- ================================================================ --}}

<div class="row g-3 mb-4">

    {{-- Risk Score --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:{{ $riskGradient }};border-color:{{ $riskColor }}33;">
            <div class="stat-icon" style="background:{{ $riskColor }}22;color:{{ $riskColor }};">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Risk Score</div>
                <div class="stat-value" style="color:{{ $riskColor }};">{{ $riskScore }}</div>
                <div class="stat-sub">
                    <span class="risk-pill" style="background:{{ $riskColor }}22;color:{{ $riskColor }};">
                        {{ $riskLevel }}
                    </span>
                    @if($risk)
                        <span class="text-muted ms-1" style="font-size:.65rem;" title="{{ $risk->updated_at }}">
                            Updated {{ $risk->updated_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Weather --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#3b82f622,#3b82f608);border-color:#3b82f633;">
            <div class="stat-icon" style="background:#3b82f622;color:#3b82f6;">
                <i class="bi bi-thermometer-half"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Temperature</div>
                <div class="stat-value" style="color:#3b82f6;">
                    @if($weather)
                        {{ number_format($weather->temperature,1) }}<span style="font-size:1rem">°C</span>
                    @else —
                    @endif
                </div>
                <div class="stat-sub text-muted" style="display:flex; flex-direction:column; gap:2px;">
                    <div>{{ $weatherIcon }} {{ $weather?->weather_main ?? 'No Data' }}</div>
                    @if($weather)
                        <div style="font-size:.65rem;" title="{{ $weather->updated_at }}">
                            Updated {{ $weather->updated_at->diffForHumans() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Exchange Rate --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf622,#8b5cf608);border-color:#8b5cf633;">
            <div class="stat-icon" style="background:#8b5cf622;color:#8b5cf6;">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Exchange Rate</div>
                <div class="stat-value" style="color:#8b5cf6;">
                    @if($currency)
                        {{ number_format($currency->exchange_rate,2) }}
                    @else —
                    @endif
                </div>
                <div class="stat-sub text-muted" style="display:flex; flex-direction:column; gap:2px;">
                    <div>vs USD · {{ $country->currency_code ?? 'N/A' }}</div>
                    @if($currency)
                        <div style="font-size:.65rem;" title="{{ $currency->updated_at }}">
                            Updated {{ $currency->updated_at->diffForHumans() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Total Ports --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#06b6d422,#06b6d408);border-color:#06b6d433;">
            <div class="stat-icon" style="background:#06b6d422;color:#06b6d4;">
                <i class="bi bi-anchor"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Ports</div>
                <div class="stat-value" style="color:#06b6d4;">{{ $totalPorts }}</div>
                <div class="stat-sub text-muted">
                    <span style="color:#10b981">{{ $activePorts }} active</span> · <span style="color:#ef4444">{{ $inactivePorts }} inactive</span>
                    @if($country->ports->isNotEmpty())
                        <div style="font-size:.65rem; margin-top:2px;" title="{{ $country->ports->max('updated_at') }}">
                            Updated {{ $country->ports->max('updated_at')->diffForHumans() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ================================================================ --}}
{{-- NEWS + PORT STATISTICS                                            --}}
{{-- ================================================================ --}}

<div class="row g-3 mb-4">

    {{-- Latest News --}}
    <div class="col-lg-7">
        <div class="panel-card h-100">
            <div class="panel-header">
                <span class="panel-icon" style="background:#f59e0b22;color:#f59e0b;"><i class="bi bi-newspaper"></i></span>
                <span class="panel-title">Latest Intelligence</span>
                <div class="ms-auto d-flex align-items-center gap-2">
                    @if($country->newsCaches->isNotEmpty())
                        <span class="text-muted" style="font-size:.65rem;" title="{{ $country->newsCaches->max('updated_at') }}">
                            Updated {{ $country->newsCaches->max('updated_at')->diffForHumans() }}
                        </span>
                    @endif
                    <span class="badge" style="background:#f59e0b22;color:#f59e0b;">
                        {{ $country->newsCaches->count() }} articles
                    </span>
                </div>
            </div>
            <div class="panel-body">
                @forelse($country->newsCaches->sortByDesc('published_at')->take(5) as $news)
                    <div class="news-item {{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                        <a href="{{ $news->url }}" target="_blank" class="news-title">
                            {{ $news->title }}
                        </a>
                        <div class="news-meta mt-1">
                            <i class="bi bi-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($news->published_at)->format('d M Y · H:i') }}
                            @if($news->source)
                                · <span class="news-source">{{ $news->source }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-newspaper"></i>
                        <p>No news available for this country.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Port Statistics --}}
    <div class="col-lg-5">
        <div class="panel-card h-100">
            <div class="panel-header">
                <span class="panel-icon" style="background:#06b6d422;color:#06b6d4;"><i class="bi bi-anchor"></i></span>
                <span class="panel-title">Port Statistics</span>
            </div>
            <div class="panel-body">

                {{-- Stat numbers --}}
                <div class="port-stat-grid mb-4">
                    <div class="port-stat-item" style="--c:#06b6d4;">
                        <div class="port-stat-num">{{ $totalPorts }}</div>
                        <div class="port-stat-lbl">Total</div>
                    </div>
                    <div class="port-stat-item" style="--c:#10b981;">
                        <div class="port-stat-num">{{ $activePorts }}</div>
                        <div class="port-stat-lbl">Active</div>
                    </div>
                    <div class="port-stat-item" style="--c:#ef4444;">
                        <div class="port-stat-num">{{ $inactivePorts }}</div>
                        <div class="port-stat-lbl">Inactive</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                @if($totalPorts > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small text-muted">
                            <span>Active ratio</span>
                            <span>{{ number_format(($activePorts/$totalPorts)*100,0) }}%</span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:99px;">
                            <div class="progress-bar bg-success" style="width:{{ ($activePorts/$totalPorts)*100 }}%;border-radius:99px;"></div>
                        </div>
                    </div>
                @endif

                <hr style="opacity:.08;">

                <div class="row text-center g-2">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">First Port</small>
                        <strong class="small">{{ optional($country->ports->first())->port_name ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Latest Port</small>
                        <strong class="small">{{ optional($country->ports->last())->port_name ?? '—' }}</strong>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ================================================================ --}}
{{-- MAPS ROW                                                          --}}
{{-- ================================================================ --}}

<div class="row g-3 mb-4">

    {{-- Country Location Map --}}
    <div class="col-lg-5">
        <div class="panel-card h-100">
            <div class="panel-header">
                <span class="panel-icon" style="background:#10b98122;color:#10b981;"><i class="bi bi-geo-alt-fill"></i></span>
                <span class="panel-title">Country Location</span>
            </div>
            <div class="panel-body p-0">
                <div id="countryMap" style="height:380px;border-radius:0 0 16px 16px;"></div>
            </div>
        </div>
    </div>

    {{-- Port Distribution Map --}}
    <div class="col-lg-7">
        <div class="panel-card h-100">
            <div class="panel-header">
                <span class="panel-icon" style="background:#06b6d422;color:#06b6d4;"><i class="bi bi-map-fill"></i></span>
                <span class="panel-title">Port Distribution Map</span>
                <span class="ms-auto badge" style="background:#06b6d422;color:#06b6d4;">
                    {{ $totalPorts }} Ports
                </span>
            </div>
            <div class="panel-body p-0">
                <div id="countryPortMap" style="height:380px;border-radius:0 0 16px 16px;"></div>
            </div>
        </div>
    </div>

</div>

{{-- ================================================================ --}}
{{-- PORT CARDS                                                        --}}
{{-- ================================================================ --}}

<div class="section-divider mb-4">
    <div class="section-divider-inner">
        <i class="bi bi-anchor me-2"></i>
        Registered Ports
        <span class="ms-2 badge" style="background:#06b6d422;color:#06b6d4;">{{ $totalPorts }}</span>
    </div>
</div>

<div class="row g-3 mb-4">

@forelse ($country->ports as $port)

    <div class="col-xl-4 col-md-6">
        <div class="port-card-premium">
            <div class="pcp-header">
                <div>
                    <div class="pcp-name">🚢 {{ $port->port_name }}</div>
                    <div class="pcp-city">
                        <i class="bi bi-pin-map me-1"></i>{{ $port->city ?: 'Unknown City' }}
                    </div>
                </div>
                <span class="pcp-status {{ strtoupper($port->status ?? '') === 'ACTIVE' ? 'active' : 'inactive' }}">
                    {{ strtoupper($port->status ?? 'N/A') === 'ACTIVE' ? '● ACTIVE' : '○ ' . ($port->status ?? 'N/A') }}
                </span>
            </div>

            <div class="pcp-details">
                <div class="pcp-detail-item">
                    <i class="bi bi-upc me-1 text-muted"></i>
                    <span class="text-muted">Code</span>
                    <strong class="ms-auto">{{ $port->port_code ?: '—' }}</strong>
                </div>
                <div class="pcp-detail-item">
                    <i class="bi bi-clock me-1 text-muted"></i>
                    <span class="text-muted">Timezone</span>
                    <strong class="ms-auto">{{ $port->timezone ?: '—' }}</strong>
                </div>
                @if($port->latitude && $port->longitude)
                <div class="pcp-detail-item">
                    <i class="bi bi-crosshair me-1 text-muted"></i>
                    <span class="text-muted">Coords</span>
                    <strong class="ms-auto" style="font-size:.78rem;">
                        {{ number_format($port->latitude,3) }}, {{ number_format($port->longitude,3) }}
                    </strong>
                </div>
                @endif
            </div>

            @if($port->description)
                <p class="pcp-desc">{{ \Illuminate\Support\Str::limit($port->description, 100) }}</p>
            @endif

            <a href="{{ route('ports.show', $port) }}" class="pcp-btn">
                <i class="bi bi-geo-alt-fill me-1"></i> Explore Port
            </a>
        </div>
    </div>

@empty

    <div class="col-12">
        <div class="empty-state-large">
            <div class="empty-icon">⚓</div>
            <h5>No Ports Registered</h5>
            <p class="text-muted">There are no ports registered for <strong>{{ $country->name }}</strong> yet.</p>
        </div>
    </div>

@endforelse

</div>

@push('styles')
<style>

/* ── Google Font ──────────────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
    --surface      : #ffffff;
    --surface-alt  : #f8fafc;
    --border       : rgba(0,0,0,.07);
    --text-main    : #0f172a;
    --text-muted   : #64748b;
    --radius-lg    : 18px;
    --radius-md    : 12px;
    --shadow-sm    : 0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.05);
    --shadow-md    : 0 4px 20px rgba(0,0,0,.10), 0 1px 4px rgba(0,0,0,.06);
}

/* ── Hero ─────────────────────────────────────────────────── */
.country-hero {
    background    : linear-gradient(135deg, #1e293b 0%, #0f172a 60%, #1a1a2e 100%);
    border-radius : var(--radius-lg);
    padding       : 0;
    overflow      : hidden;
    position      : relative;
    box-shadow    : var(--shadow-md);
}
.country-hero::before {
    content    : '';
    position   : absolute;
    inset      : 0;
    background : radial-gradient(ellipse at top right, rgba(99,102,241,.25) 0%, transparent 60%),
                 radial-gradient(ellipse at bottom left, rgba(6,182,212,.15) 0%, transparent 50%);
    pointer-events : none;
}
.country-hero-inner {
    position : relative;
    padding  : 2rem 2rem 1.75rem;
}
.hero-back-btn {
    display         : flex;
    align-items     : center;
    justify-content : center;
    width           : 38px;
    height          : 38px;
    border-radius   : 50%;
    background      : rgba(255,255,255,.12);
    color           : #fff;
    text-decoration : none;
    font-size       : 1.1rem;
    transition      : background .2s;
    flex-shrink     : 0;
}
.hero-back-btn:hover { background: rgba(255,255,255,.22); color:#fff; }
.country-flag-circle {
    width           : 52px;
    height          : 52px;
    border-radius   : 50%;
    background      : rgba(255,255,255,.1);
    backdrop-filter : blur(8px);
    display         : flex;
    align-items     : center;
    justify-content : center;
    font-size       : 1.8rem;
    flex-shrink     : 0;
}
.hero-title {
    font-family : 'Inter', sans-serif;
    font-weight : 800;
    font-size   : 1.75rem;
    color       : #fff;
    letter-spacing: -.02em;
}
.hero-sub {
    color       : rgba(255,255,255,.55);
    font-size   : .875rem;
}
.hero-badge {
    display         : inline-flex;
    align-items     : center;
    padding         : .3rem .75rem;
    border-radius   : 99px;
    background      : rgba(255,255,255,.1);
    backdrop-filter : blur(4px);
    color           : rgba(255,255,255,.85);
    font-size       : .78rem;
    font-weight     : 500;
    border          : 1px solid rgba(255,255,255,.15);
}

/* ── Stat Cards ───────────────────────────────────────────── */
.stat-card {
    display       : flex;
    align-items   : center;
    gap           : 1rem;
    padding       : 1.25rem;
    border-radius : var(--radius-lg);
    border        : 1.5px solid transparent;
    background    : var(--surface);
    box-shadow    : var(--shadow-sm);
    transition    : transform .2s, box-shadow .2s;
}
.stat-card:hover { transform:translateY(-4px); box-shadow: var(--shadow-md); }
.stat-icon {
    width           : 48px;
    height          : 48px;
    border-radius   : 14px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    font-size       : 1.4rem;
    flex-shrink     : 0;
}
.stat-body { flex:1; min-width:0; }
.stat-label {
    font-size   : .72rem;
    font-weight : 600;
    text-transform : uppercase;
    letter-spacing : .08em;
    color       : var(--text-muted);
    margin-bottom : .1rem;
}
.stat-value {
    font-size   : 1.75rem;
    font-weight : 800;
    line-height : 1.1;
    font-family : 'Inter', sans-serif;
    margin-bottom: .2rem;
}
.stat-sub { font-size: .78rem; color: var(--text-muted); }
.risk-pill {
    display     : inline-block;
    padding     : .15rem .55rem;
    border-radius: 99px;
    font-size   : .72rem;
    font-weight : 700;
    letter-spacing: .06em;
}

/* ── Panel Cards ──────────────────────────────────────────── */
.panel-card {
    background    : var(--surface);
    border-radius : var(--radius-lg);
    border        : 1.5px solid var(--border);
    box-shadow    : var(--shadow-sm);
    overflow      : hidden;
    display       : flex;
    flex-direction: column;
}
.panel-header {
    display     : flex;
    align-items : center;
    gap         : .6rem;
    padding     : 1rem 1.25rem;
    border-bottom: 1.5px solid var(--border);
    background  : var(--surface-alt);
}
.panel-icon {
    width           : 32px;
    height          : 32px;
    border-radius   : 8px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    font-size       : 1rem;
    flex-shrink     : 0;
}
.panel-title {
    font-weight : 700;
    font-size   : .9rem;
    color       : var(--text-main);
    font-family : 'Inter', sans-serif;
}
.panel-body { padding:1.25rem; flex:1; }

/* ── News ─────────────────────────────────────────────────── */
.news-item .news-title {
    font-weight     : 600;
    font-size       : .875rem;
    color           : var(--text-main);
    text-decoration : none;
    line-height     : 1.4;
    display         : block;
    transition      : color .15s;
}
.news-item .news-title:hover { color:#6366f1; }
.news-meta { font-size:.72rem; color:var(--text-muted); }
.news-source { font-weight:600; color:#6366f1; }

/* ── Port Stat Grid ───────────────────────────────────────── */
.port-stat-grid {
    display        : grid;
    grid-template-columns: repeat(3,1fr);
    gap            : .75rem;
}
.port-stat-item {
    text-align    : center;
    padding       : .75rem .5rem;
    border-radius : var(--radius-md);
    background    : color-mix(in srgb, var(--c) 10%, transparent);
    border        : 1.5px solid color-mix(in srgb, var(--c) 25%, transparent);
}
.port-stat-num {
    font-size   : 1.75rem;
    font-weight : 800;
    color       : var(--c);
    line-height : 1;
    font-family : 'Inter', sans-serif;
}
.port-stat-lbl {
    font-size     : .72rem;
    font-weight   : 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color         : var(--text-muted);
    margin-top    : .25rem;
}

/* ── Section Divider ──────────────────────────────────────── */
.section-divider { position:relative; }
.section-divider-inner {
    display        : inline-flex;
    align-items    : center;
    padding        : .45rem 1.1rem;
    background     : linear-gradient(135deg,#0f172a,#1e293b);
    color          : #fff;
    border-radius  : 99px;
    font-size      : .82rem;
    font-weight    : 700;
    letter-spacing : .04em;
    box-shadow     : 0 4px 16px rgba(15,23,42,.25);
}

/* ── Port Cards Premium ───────────────────────────────────── */
.port-card-premium {
    background    : var(--surface);
    border        : 1.5px solid var(--border);
    border-radius : var(--radius-lg);
    padding       : 1.25rem;
    box-shadow    : var(--shadow-sm);
    transition    : transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
    height        : 100%;
    display       : flex;
    flex-direction: column;
}
.port-card-premium:hover {
    transform  : translateY(-6px);
    box-shadow : 0 12px 32px rgba(6,182,212,.12), 0 4px 12px rgba(0,0,0,.08);
    border-color: rgba(6,182,212,.3);
}
.pcp-header {
    display         : flex;
    justify-content : space-between;
    align-items     : flex-start;
    margin-bottom   : 1rem;
}
.pcp-name {
    font-weight   : 700;
    font-size     : 1rem;
    color         : var(--text-main);
    line-height   : 1.3;
}
.pcp-city {
    font-size  : .78rem;
    color      : var(--text-muted);
    margin-top : .2rem;
}
.pcp-status {
    font-size   : .7rem;
    font-weight : 700;
    padding     : .25rem .6rem;
    border-radius: 99px;
    flex-shrink : 0;
    margin-left : .5rem;
}
.pcp-status.active   { background:#10b98122; color:#10b981; border:1.5px solid #10b98133; }
.pcp-status.inactive { background:#6b728022; color:#6b7280; border:1.5px solid #6b728033; }
.pcp-details {
    background    : var(--surface-alt);
    border-radius : var(--radius-md);
    padding       : .75rem;
    margin-bottom : .75rem;
    flex          : 1;
}
.pcp-detail-item {
    display     : flex;
    align-items : center;
    font-size   : .8rem;
    padding     : .3rem 0;
    border-bottom: 1px solid var(--border);
}
.pcp-detail-item:last-child { border-bottom:none; }
.pcp-desc {
    font-size   : .78rem;
    color       : var(--text-muted);
    line-height : 1.5;
    margin-bottom: .75rem;
}
.pcp-btn {
    display         : flex;
    align-items     : center;
    justify-content : center;
    padding         : .6rem 1rem;
    border-radius   : var(--radius-md);
    background      : linear-gradient(135deg,#0f172a,#1e293b);
    color           : #fff;
    font-size       : .82rem;
    font-weight     : 600;
    text-decoration : none;
    transition      : opacity .2s, transform .2s;
    margin-top      : auto;
}
.pcp-btn:hover { opacity:.85; color:#fff; transform:scale(1.02); }

/* ── Empty States ─────────────────────────────────────────── */
.empty-state {
    text-align  : center;
    padding     : 2rem;
    color       : var(--text-muted);
}
.empty-state i { font-size:2rem; display:block; margin-bottom:.5rem; opacity:.4; }
.empty-state-large {
    text-align    : center;
    padding       : 3rem;
    background    : var(--surface);
    border-radius : var(--radius-lg);
    border        : 2px dashed var(--border);
}
.empty-icon { font-size:3rem; margin-bottom:1rem; }

</style>
@endpush

@push('scripts')
<script>

document.addEventListener("DOMContentLoaded", function () {

    /* ──────────────────────────────────────────────────────────
     | Country Location Map
     ────────────────────────────────────────────────────────── */

    const countryMap = L.map('countryMap', { zoomControl: true });

    countryMap.setView([{{ $country->latitude }}, {{ $country->longitude }}], 5);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution : '© OpenStreetMap, © CartoDB',
        maxZoom     : 18
    }).addTo(countryMap);

    const customIcon = L.divIcon({
        html      : `<div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#06b6d4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;box-shadow:0 4px 16px rgba(99,102,241,.4);border:3px solid #fff;">🌍</div>`,
        iconSize  : [36,36],
        iconAnchor: [18,18],
        className : ''
    });

    L.marker([{{ $country->latitude }}, {{ $country->longitude }}], { icon: customIcon })
        .addTo(countryMap)
        .bindPopup(`
            <div style="min-width:200px;font-family:Inter,sans-serif;">
                <h6 style="font-weight:700;margin-bottom:.5rem;">🌍 {{ addslashes($country->name) }}</h6>
                <table style="width:100%;font-size:.8rem;border-collapse:collapse;">
                    <tr><td style="color:#64748b;padding:.15rem 0;">Capital</td><td style="font-weight:600;">{{ addslashes($country->capital ?? '—') }}</td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">Region</td><td style="font-weight:600;">{{ addslashes($country->region ?? '—') }}</td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">ISO3</td><td style="font-weight:600;">{{ $country->iso3 }}</td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">Ports</td><td style="font-weight:600;">{{ $totalPorts }}</td></tr>
                </table>
            </div>
        `)
        .openPopup();

    /* ──────────────────────────────────────────────────────────
     | Port Distribution Map
     ────────────────────────────────────────────────────────── */

    const ports   = @json($country->ports);
    const portMap = L.map('countryPortMap', { zoomControl: true });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution : '© OpenStreetMap, © CartoDB',
        maxZoom     : 18
    }).addTo(portMap);

    const bounds = [];

    ports.forEach(port => {
        if (!port.latitude || !port.longitude) return;

        const lat = parseFloat(port.latitude);
        const lng = parseFloat(port.longitude);
        const isActive = (port.status ?? '').toUpperCase() === 'ACTIVE';

        bounds.push([lat, lng]);

        L.circleMarker([lat, lng], {
            radius      : 9,
            color       : '#fff',
            weight      : 2.5,
            fillColor   : isActive ? '#10b981' : '#6b7280',
            fillOpacity : 0.9
        })
        .bindPopup(`
            <div style="min-width:200px;font-family:Inter,sans-serif;">
                <h6 style="font-weight:700;margin-bottom:.5rem;">🚢 ${port.port_name}</h6>
                <table style="width:100%;font-size:.8rem;border-collapse:collapse;">
                    <tr><td style="color:#64748b;padding:.15rem 0;">City</td><td style="font-weight:600;">${port.city ?? '—'}</td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">Code</td><td style="font-weight:600;">${port.port_code ?? '—'}</td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">Status</td><td><span style="background:${isActive?'#10b98122':'#6b728022'};color:${isActive?'#10b981':'#6b7280'};padding:.1rem .4rem;border-radius:99px;font-weight:700;font-size:.7rem;">${port.status ?? 'N/A'}</span></td></tr>
                    <tr><td style="color:#64748b;padding:.15rem 0;">Timezone</td><td style="font-weight:600;">${port.timezone ?? '—'}</td></tr>
                </table>
            </div>
        `)
        .addTo(portMap);
    });

    if (bounds.length > 0) {
        portMap.fitBounds(bounds, { padding: [40, 40] });
    } else {
        portMap.setView([{{ $country->latitude }}, {{ $country->longitude }}], 5);
    }

});

</script>
@endpush

@endsection