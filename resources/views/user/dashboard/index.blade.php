@extends('layouts.dashboard')

@section('title', 'Executive Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-speedometer2 me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Executive Dashboard
        </h1>
        <p class="page-header-sub mb-0">
            Welcome back, <strong>{{ auth()->user()->name }}</strong> —
            Global supply chain overview as of {{ now()->format('d M Y, H:i') }} WIB
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge d-flex align-items-center gap-1 px-3 py-2"
              style="background:var(--success-bg);color:var(--success);border:1px solid rgba(22,163,74,.2);border-radius:99px;font-size:.72rem;font-weight:700;">
            <i class="bi bi-circle-fill" style="font-size:.45rem;animation:pulse-live 2s infinite;"></i>
            Live Data
        </span>
        <a href="{{ route('countries.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-globe-americas"></i> View Countries
        </a>
    </div>
</div>

{{-- ── STAT CARDS ───────────────────────────────────────── --}}
<div class="dashboard-grid mb-4">

    @include('components.stat-card', [
        'title'    => 'Total Countries',
        'value'    => $totalCountries,
        'icon'     => 'bi bi-globe2',
        'color'    => '#2563EB',
        'subtitle' => 'Monitored globally',
    ])

    @include('components.stat-card', [
        'title'    => 'High Risk',
        'value'    => $highRisk,
        'icon'     => 'bi bi-exclamation-triangle-fill',
        'color'    => '#DC2626',
        'subtitle' => 'Immediate attention',
    ])

    @include('components.stat-card', [
        'title'    => 'Medium Risk',
        'value'    => $mediumRisk,
        'icon'     => 'bi bi-activity',
        'color'    => '#D97706',
        'subtitle' => 'Monitor closely',
    ])

    @include('components.stat-card', [
        'title'    => 'Low Risk',
        'value'    => $lowRisk,
        'icon'     => 'bi bi-shield-check',
        'color'    => '#16A34A',
        'subtitle' => 'Stable conditions',
    ])

</div>

{{-- ── WORLD RISK MAP ───────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-map-fill"></i>
        </div>
        <span class="panel-title fw-bold">Global Risk Map</span>
        <div class="ms-auto d-flex align-items-center gap-2">
            {{-- Map Legend --}}
            <div class="d-none d-md-flex align-items-center gap-3" style="font-size:.72rem;font-weight:600;">
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:50%;background:#DC2626;display:inline-block;"></span>
                    High
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:50%;background:#D97706;display:inline-block;"></span>
                    Medium
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:50%;background:#16A34A;display:inline-block;"></span>
                    Low
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:50%;background:#94A3B8;display:inline-block;"></span>
                    N/A
                </span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="worldMap"></div>
    </div>
</div>

<script>window.countriesMap = @json($countriesMap);</script>

{{-- ── CHARTS ROW ───────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(220,38,38,.08);color:#DC2626;">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Risk Distribution</span>
            </div>
            <div class="card-body">
                <div id="riskDistributionChart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Average Risk by Region</span>
            </div>
            <div class="card-body">
                <div id="riskRegionChart"></div>
            </div>
        </div>
    </div>

</div>

<script>
window.riskDistribution = @json($riskDistribution);
window.riskByRegion     = @json($riskByRegion);
</script>

{{-- ── TOP RISK + LATEST NEWS ──────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Top Risk Countries --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(220,38,38,.08);color:#DC2626;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Top 5 Highest Risk Countries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-enterprise mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Country</th>
                                <th>Score</th>
                                <th>Level</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topRiskCountries as $i => $risk)
                                <tr>
                                    <td>
                                        <span style="font-size:.72rem;font-weight:700;color:var(--text-subtle);">
                                            {{ $i + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-weight:600;color:var(--text);">
                                            {{ $risk->country->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-weight:700;font-family:monospace;font-size:.85rem;">
                                            {{ number_format($risk->risk_score, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($risk->risk_level == 'HIGH')
                                            <span class="badge badge-risk-high" style="font-size:.7rem;padding:.3rem .6rem;border-radius:99px;">HIGH</span>
                                        @elseif($risk->risk_level == 'MEDIUM')
                                            <span class="badge badge-risk-medium" style="font-size:.7rem;padding:.3rem .6rem;border-radius:99px;">MED</span>
                                        @else
                                            <span class="badge badge-risk-low" style="font-size:.7rem;padding:.3rem .6rem;border-radius:99px;">LOW</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('countries.show', $risk->country) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           style="padding:3px 8px;font-size:.7rem;">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-muted" style="font-size:.82rem;">
                                        <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                                        No risk data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest News --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(217,119,6,.08);color:#D97706;">
                    <i class="bi bi-newspaper"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Latest Intelligence</span>
                <a href="{{ route('news.index') }}"
                   class="ms-auto btn btn-sm btn-outline-secondary"
                   style="font-size:.72rem;padding:3px 10px;">
                    View all <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-enterprise mb-0">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Headline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestNews as $news)
                                <tr>
                                    <td style="white-space:nowrap;">
                                        <span class="badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:600;">
                                            {{ $news->country->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ $news->url }}" target="_blank"
                                           style="font-weight:600;font-size:.8rem;color:var(--text);text-decoration:none;display:block;line-height:1.35;"
                                           onmouseover="this.style.color='var(--primary-light)'"
                                           onmouseout="this.style.color='var(--text)'">
                                            {{ \Illuminate\Support\Str::limit($news->title, 65) }}
                                        </a>
                                        <small style="color:var(--text-subtle);font-size:.68rem;">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($news->published_at)->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-muted" style="font-size:.82rem;">
                                        <i class="bi bi-newspaper d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                                        No news available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── WEATHER + CURRENCY ───────────────────────────────── --}}
<div class="row g-3 mb-2">

    {{-- Weather Alerts --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(220,38,38,.08);color:#DC2626;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Active Weather Alerts</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-enterprise mb-0">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Weather Condition</th>
                                <th>Temp</th>
                                <th>Alert Level</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weatherAlerts as $alert)
                                <tr>
                                    <td style="font-weight:600;font-size:.82rem;vertical-align:middle;">
                                        {{ $alert->country->name }}
                                    </td>
                                    <td style="font-size:.78rem;color:var(--text-muted);vertical-align:middle;" title="{{ $alert->description }}">
                                        <div style="font-weight:600;color:var(--text);">{{ $alert->title }}</div>
                                        <div style="font-size:.7rem;color:var(--text-subtle);">{{ $alert->weather_condition }}</div>
                                    </td>
                                    <td style="font-weight:700;font-size:.82rem;font-family:monospace;vertical-align:middle;">
                                        {{ $alert->temperature !== null ? number_format($alert->temperature, 1) . '°C' : '—' }}
                                    </td>
                                    <td style="vertical-align:middle;">
                                        @if(strtoupper($alert->severity) === 'CRITICAL')
                                            <span class="badge" style="background:#7F1D1D;color:#FEE2E2;border-radius:99px;font-size:.68rem;padding:.25rem .55rem;font-weight:700;">CRITICAL</span>
                                        @elseif(strtoupper($alert->severity) === 'HIGH')
                                            <span class="badge badge-risk-high" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">HIGH</span>
                                        @elseif(strtoupper($alert->severity) === 'MEDIUM')
                                            <span class="badge badge-risk-medium" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">MEDIUM</span>
                                        @else
                                            <span class="badge badge-risk-low" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">LOW</span>
                                        @endif
                                    </td>
                                    <td style="font-size:.72rem;color:var(--text-subtle);vertical-align:middle;">
                                        {{ \Carbon\Carbon::parse($alert->generated_at)->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted" style="font-size:.82rem;">
                                        <i class="bi bi-shield-fill-check d-block mb-2" style="font-size:2rem;color:#16A34A;opacity:.7;"></i>
                                        <strong>No significant weather risks detected.</strong>
                                        <p class="mb-0 text-subtle" style="font-size:.72rem;">Global supply chain weather factors are currently stable.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Currency Updates --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(22,163,74,.08);color:#16A34A;">
                    <i class="bi bi-currency-exchange"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Latest Currency Updates</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-enterprise mb-0">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Currency</th>
                                <th>Rate</th>
                                <th>Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestRates as $rate)
                                <tr>
                                    <td style="font-weight:600;font-size:.82rem;">{{ $rate->country->name }}</td>
                                    <td>
                                        <span class="badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;">
                                            {{ $rate->country->currency_code ?? '—' }}
                                        </span>
                                    </td>
                                    <td style="font-weight:700;font-family:monospace;font-size:.82rem;">
                                        {{ number_format($rate->exchange_rate, 2) }}
                                    </td>
                                    <td>
                                        @if(($rate->change_percentage ?? 0) >= 0)
                                            <span style="color:#16A34A;font-weight:700;font-size:.78rem;">
                                                <i class="bi bi-caret-up-fill"></i>
                                                +{{ number_format($rate->change_percentage ?? 0, 2) }}%
                                            </span>
                                        @else
                                            <span style="color:#DC2626;font-weight:700;font-size:.78rem;">
                                                <i class="bi bi-caret-down-fill"></i>
                                                {{ number_format($rate->change_percentage ?? 0, 2) }}%
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-muted" style="font-size:.82rem;">
                                        <i class="bi bi-currency-dollar d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                                        No currency data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ── Risk Distribution Donut ─────────────────────────── */
    if (typeof ApexCharts !== "undefined" && document.querySelector("#riskDistributionChart")) {
        new ApexCharts(document.querySelector("#riskDistributionChart"), {
            chart  : { type: "donut", height: 300, fontFamily: "Inter, sans-serif" },
            series : [
                window.riskDistribution.high ?? 0,
                window.riskDistribution.medium ?? 0,
                window.riskDistribution.low ?? 0
            ],
            labels : ["High Risk", "Medium Risk", "Low Risk"],
            colors : ["#DC2626", "#D97706", "#16A34A"],
            legend : { position: "bottom", fontSize: "12px" },
            stroke : { show: false },
            plotOptions: {
                pie: { donut: { size: "65%", labels: { show: true, total: { show: true, label: "Total", color: "#64748B", fontSize: "12px" } } } }
            },
            tooltip: { style: { fontSize: "12px" } },
            dataLabels: { style: { fontSize: "11px", fontWeight: 600 } }
        }).render();
    }

    /* ── Risk by Region Bar ──────────────────────────────── */
    if (typeof ApexCharts !== "undefined" && document.querySelector("#riskRegionChart")) {
        new ApexCharts(document.querySelector("#riskRegionChart"), {
            chart  : { type: "bar", height: 300, fontFamily: "Inter, sans-serif", toolbar: { show: false } },
            series : [{ name: "Avg Risk Score", data: window.riskByRegion.map(i => Number(i.average_score).toFixed(1)) }],
            xaxis  : { categories: window.riskByRegion.map(i => i.region), labels: { style: { fontSize: "11px" } } },
            yaxis  : { labels: { style: { fontSize: "11px" } } },
            colors : ["#2563EB"],
            dataLabels: { enabled: false },
            plotOptions: { bar: { borderRadius: 6, columnWidth: "50%" } },
            grid   : { borderColor: "#F1F5F9", strokeDashArray: 4 },
            tooltip: { style: { fontSize: "12px" } }
        }).render();
    }

});
</script>
@endpush

@endsection