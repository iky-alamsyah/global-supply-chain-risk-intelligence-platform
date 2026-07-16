@extends('layouts.dashboard')

@section('title', 'Visualization Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-bar-chart-line me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Visualization Dashboard
        </h1>
        <p class="page-header-sub mb-0">Analytics and visual intelligence across the global supply chain network.</p>
    </div>
</div>

{{-- KPI Cards --}}
<div class="dashboard-grid mb-4">

    @include('components.stat-card', [
        'title'    => 'Total Countries',
        'value'    => $totalCountries,
        'icon'     => 'bi bi-globe2',
        'color'    => '#2563EB',
        'subtitle' => 'Monitored globally',
    ])

    @include('components.stat-card', [
        'title'    => 'Total Ports',
        'value'    => $totalPorts,
        'icon'     => 'bi bi-anchor',
        'color'    => '#0891B2',
        'subtitle' => 'Active & inactive',
    ])

    @include('components.stat-card', [
        'title'    => 'Total News',
        'value'    => $totalNews,
        'icon'     => 'bi bi-newspaper',
        'color'    => '#D97706',
        'subtitle' => 'Articles indexed',
    ])

    @include('components.stat-card', [
        'title'    => 'Avg Risk Score',
        'value'    => $averageRisk,
        'icon'     => 'bi bi-shield-exclamation',
        'color'    => '#DC2626',
        'subtitle' => 'Global average',
    ])

</div>

{{-- Row 1: Risk Distribution + Risk by Region --}}
<div class="row g-3 mb-4">

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(220,38,38,.08);color:#DC2626;">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Risk Distribution</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="riskChart" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Countries by Region</span>
            </div>
            <div class="card-body">
                <canvas id="regionChart" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- Row 2: Top Risk + Top Ports --}}
<div class="row g-3 mb-4">

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(220,38,38,.08);color:#DC2626;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Top 10 Highest Risk Countries</span>
            </div>
            <div class="card-body">
                <canvas id="topRiskChart" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
                    <i class="bi bi-anchor"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Top 10 Countries by Ports</span>
            </div>
            <div class="card-body">
                <canvas id="portChart" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- Row 3: News Category + Weather --}}
<div class="row g-3 mb-4">

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(217,119,6,.08);color:#D97706;">
                    <i class="bi bi-newspaper"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">News Category Distribution</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="newsChart" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="section-icon" style="background:rgba(8,145,178,.08);color:#0891B2;">
                    <i class="bi bi-thermometer-half"></i>
                </div>
                <span class="fw-bold" style="font-size:.9rem;">Avg Temperature by Region</span>
            </div>
            <div class="card-body">
                <canvas id="weatherChart" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- Global Risk Map --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-map-fill"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Global Risk Map</span>
        <div class="ms-auto d-flex align-items-center gap-3" style="font-size:.72rem;font-weight:600;">
            <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:50%;background:#DC2626;display:inline-block;"></span>High</span>
            <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:50%;background:#D97706;display:inline-block;"></span>Medium</span>
            <span class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:50%;background:#16A34A;display:inline-block;"></span>Low</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="riskMap" style="height:520px;border-radius:0 0 var(--radius-lg) var(--radius-lg);"></div>
    </div>
</div>

@push('scripts')
<script>
const countriesData  = @json($countries);
const topRisk        = @json($topRisk);
const topPorts       = @json($topPorts);
const newsCategory   = @json($newsCategory);
const weatherRegion  = @json($weatherRegion);

const COLORS = {
    primary   : '#2563EB',
    danger    : '#DC2626',
    warning   : '#D97706',
    success   : '#16A34A',
    info      : '#0891B2',
    muted     : '#94A3B8',
    palette   : ['#2563EB','#0891B2','#16A34A','#D97706','#DC2626','#8B5CF6','#EC4899','#F97316','#14B8A6','#6366F1']
};

const CHART_DEFAULTS = {
    font        : { family: 'Inter, sans-serif', size: 12 },
    gridColor   : 'rgba(15,23,42,.06)',
    tooltipStyle: { backgroundColor: '#0F172A', titleFont: { size: 12, weight: '700' }, bodyFont: { size: 11 }, padding: 10, cornerRadius: 8 }
};

/* ── Risk Pie ─────────────────────────────────────────── */
new Chart(document.getElementById('riskChart'), {
    type : 'pie',
    data : {
        labels  : ['High Risk', 'Medium Risk', 'Low Risk'],
        datasets: [{ data: [{{ $high }}, {{ $medium }}, {{ $low }}], backgroundColor: [COLORS.danger, COLORS.warning, COLORS.success], borderWidth: 0 }]
    },
    options: {
        responsive: true,
        plugins: {
            legend  : { position: 'bottom', labels: { font: CHART_DEFAULTS.font, padding: 14, usePointStyle: true } },
            tooltip : CHART_DEFAULTS.tooltipStyle
        }
    }
});

/* ── Countries by Region ──────────────────────────────── */
const regionCount = {};
countriesData.forEach(c => { regionCount[c.region] = (regionCount[c.region] || 0) + 1; });
new Chart(document.getElementById('regionChart'), {
    type : 'bar',
    data : {
        labels  : Object.keys(regionCount),
        datasets: [{ label: 'Countries', data: Object.values(regionCount), backgroundColor: COLORS.primary, borderRadius: 6 }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false }, tooltip: CHART_DEFAULTS.tooltipStyle },
        scales: {
            x: { grid: { display: false }, ticks: { font: CHART_DEFAULTS.font } },
            y: { beginAtZero: true, grid: { color: CHART_DEFAULTS.gridColor }, ticks: { font: CHART_DEFAULTS.font } }
        }
    }
});

/* ── Top Risk Countries ───────────────────────────────── */
new Chart(document.getElementById('topRiskChart'), {
    type : 'bar',
    data : {
        labels  : topRisk.map(c => c.name),
        datasets: [{ label: 'Risk Score', data: topRisk.map(c => c.risk_score?.risk_score ?? 0), backgroundColor: COLORS.danger, borderRadius: 6 }]
    },
    options: {
        indexAxis: 'y', responsive: true,
        plugins: { legend: { display: false }, tooltip: CHART_DEFAULTS.tooltipStyle },
        scales: {
            x: { beginAtZero: true, max: 100, grid: { color: CHART_DEFAULTS.gridColor }, ticks: { font: CHART_DEFAULTS.font } },
            y: { grid: { display: false }, ticks: { font: CHART_DEFAULTS.font } }
        }
    }
});

/* ── Top Ports ────────────────────────────────────────── */
new Chart(document.getElementById('portChart'), {
    type : 'bar',
    data : {
        labels  : topPorts.map(c => c.name),
        datasets: [{ label: 'Ports', data: topPorts.map(c => c.ports_count), backgroundColor: COLORS.info, borderRadius: 6 }]
    },
    options: {
        indexAxis: 'y', responsive: true,
        plugins: { legend: { display: false }, tooltip: CHART_DEFAULTS.tooltipStyle },
        scales: {
            x: { beginAtZero: true, grid: { color: CHART_DEFAULTS.gridColor }, ticks: { font: CHART_DEFAULTS.font } },
            y: { grid: { display: false }, ticks: { font: CHART_DEFAULTS.font } }
        }
    }
});

/* ── News Category ────────────────────────────────────── */
new Chart(document.getElementById('newsChart'), {
    type : 'doughnut',
    data : {
        labels  : newsCategory.map(i => i.category || 'Uncategorized'),
        datasets: [{ data: newsCategory.map(i => i.total), backgroundColor: COLORS.palette, borderWidth: 0 }]
    },
    options: {
        responsive: true,
        plugins: {
            legend : { position: 'bottom', labels: { font: CHART_DEFAULTS.font, padding: 12, usePointStyle: true } },
            tooltip: CHART_DEFAULTS.tooltipStyle
        }
    }
});

/* ── Weather by Region ────────────────────────────────── */
new Chart(document.getElementById('weatherChart'), {
    type : 'line',
    data : {
        labels  : Object.keys(weatherRegion),
        datasets: [{ label: 'Avg Temp (°C)', data: Object.values(weatherRegion), borderColor: COLORS.info, backgroundColor: 'rgba(8,145,178,.08)', borderWidth: 2.5, tension: 0.4, fill: true, pointRadius: 5, pointBackgroundColor: COLORS.info }]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { font: CHART_DEFAULTS.font, usePointStyle: true } }, tooltip: CHART_DEFAULTS.tooltipStyle },
        scales: {
            x: { grid: { display: false }, ticks: { font: CHART_DEFAULTS.font } },
            y: { grid: { color: CHART_DEFAULTS.gridColor }, ticks: { font: CHART_DEFAULTS.font } }
        }
    }
});

/* ── Risk Map ─────────────────────────────────────────── */
const map = L.map('riskMap').setView([20, 0], 2);
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap, © CartoDB', maxZoom: 18
}).addTo(map);

countriesData.forEach(country => {
    if (!country.latitude || !country.longitude || !country.risk_score) return;

    const level = country.risk_score.risk_level;
    const color = level === 'HIGH' ? '#DC2626' : level === 'MEDIUM' ? '#D97706' : '#16A34A';

    L.circleMarker([country.latitude, country.longitude], {
        radius: 7, color: '#fff', weight: 1.5, fillColor: color, fillOpacity: 0.85
    })
    .bindPopup(`
        <div style="font-family:Inter,sans-serif;min-width:160px;">
            <div style="font-weight:700;margin-bottom:6px;">🌍 ${country.name}</div>
            <table style="width:100%;font-size:.78rem;border-collapse:collapse;">
                <tr><td style="color:#64748B;padding:.1rem 0;">Score</td><td style="font-weight:700;">${country.risk_score.risk_score}</td></tr>
                <tr><td style="color:#64748B;padding:.1rem 0;">Level</td><td style="font-weight:700;color:${color};">${level}</td></tr>
                <tr><td style="color:#64748B;padding:.1rem 0;">Region</td><td>${country.region}</td></tr>
            </table>
        </div>
    `)
    .addTo(map);
});
</script>
@endpush

@endsection