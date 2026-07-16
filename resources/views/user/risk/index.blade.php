@extends('layouts.dashboard')

@section('title', 'Risk Engine')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-activity me-2" style="color:#ef4444;font-size:1.2rem;"></i>
            Risk Engine Analysis
        </h1>
        <p class="page-header-sub mb-0">
            Comprehensive geopolitical, climate, currency, and supply chain risk intelligence ranking.
        </p>
    </div>
</div>

{{-- Chart Section --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(239,68,68,.08);color:#ef4444;">
            <i class="bi bi-bar-chart-fill"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Top 10 Risk Index Comparison (GDP, Weather, Currency, News)</span>
    </div>
    <div class="card-body">
        <div style="position: relative; height:260px; width:100%;">
            <canvas id="riskComparisonChart"></canvas>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-4">
            <label class="form-label fw-semibold">Search Country</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Country name..."
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ request()->url() }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Filter by Risk Level</label>
            <select name="level" class="form-select">
                <option value="">All Levels</option>
                <option value="LOW" @selected(request('level') == 'LOW')>LOW</option>
                <option value="MEDIUM" @selected(request('level') == 'MEDIUM')>MEDIUM</option>
                <option value="HIGH" @selected(request('level') == 'HIGH')>HIGH</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Sort by</label>
            <select name="sort" class="form-select">
                <option value="highest" @selected(request('sort') == 'highest')>Highest Risk First</option>
                <option value="lowest" @selected(request('sort') == 'lowest')>Lowest Risk First</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill"></i> Apply
            </button>
            @if(request()->hasAny(['search','level','sort']))
                <a href="{{ route('risk-engine.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(239,68,68,.08);color:#ef4444;">
            <i class="bi bi-activity"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Risk Analysis Matrix</span>
        <span class="ms-2 badge" style="background:rgba(239,68,68,.1);color:#ef4444;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $riskScores->total() }} countries
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">Rank</th>
                        <th>Country</th>
                        <th class="text-center">GDP Score (20%)</th>
                        <th class="text-center">Weather Score (30%)</th>
                        <th class="text-center">Currency Score (20%)</th>
                        <th class="text-center">News Score (20%)</th>
                        <th class="text-center">Risk Score</th>
                        <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riskScores as $index => $score)
                        @php
                            // Calculate global rank dynamically
                            $rank = \App\Models\CountryRiskScore::join('countries', 'country_risk_scores.country_id', '=', 'countries.id')
                                ->where('countries.is_active', true)
                                ->where('risk_score', '>', $score->risk_score)
                                ->count() + 1;
                        @endphp
                        <tr>
                            <td style="vertical-align:middle; font-weight:700; color:var(--text-subtle); padding-left:1.2rem;">
                                #{{ $rank }}
                            </td>
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="font-size:1.1rem;">🌍</div>
                                    <div>
                                        <a href="{{ route('countries.show', $score->country) }}" style="font-weight:700;color:var(--text);text-decoration:none;" class="hover-link">
                                            {{ $score->country->name }}
                                        </a>
                                        <div style="font-size:.7rem;color:var(--text-subtle);">{{ $score->country->region }} · {{ $score->country->iso3 }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-monospace" style="vertical-align:middle; font-size:.82rem;">
                                {{ number_format($score->gdp_score, 1) }}
                            </td>
                            <td class="text-center font-monospace" style="vertical-align:middle; font-size:.82rem;">
                                {{ number_format($score->weather_score, 1) }}
                            </td>
                            <td class="text-center font-monospace" style="vertical-align:middle; font-size:.82rem;">
                                {{ number_format($score->currency_score, 1) }}
                            </td>
                            <td class="text-center font-monospace" style="vertical-align:middle; font-size:.82rem;">
                                {{ number_format($score->news_score, 1) }}
                            </td>
                            <td class="text-center font-monospace" style="vertical-align:middle; font-weight:700; font-size:.88rem; color:#ef4444;">
                                {{ number_format($score->risk_score, 1) }}
                            </td>
                            <td style="vertical-align:middle;">
                                @if($score->risk_level === 'HIGH')
                                    <span class="badge badge-risk-high" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">HIGH</span>
                                @elseif($score->risk_level === 'MEDIUM')
                                    <span class="badge badge-risk-medium" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">MEDIUM</span>
                                @else
                                    <span class="badge badge-risk-low" style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;">LOW</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center text-muted" style="font-size:.82rem;">
                                <i class="bi bi-activity d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                <strong>No Risk Scores Available</strong>
                                <p class="mb-0 text-subtle" style="font-size:.72rem;">Try modifying search parameters or filter criteria.</p>
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
    {{ $riskScores->links('pagination::bootstrap-5') }}
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('riskComparisonChart').getContext('2d');
    
    const chartData = @json($chartData);

    if (!chartData || chartData.length === 0) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(c => c.country),
            datasets: [
                {
                    label: 'GDP Risk (20%)',
                    data: chartData.map(c => c.gdp),
                    backgroundColor: '#3b82f6',
                },
                {
                    label: 'Weather Risk (30%)',
                    data: chartData.map(c => c.weather),
                    backgroundColor: '#0891b2',
                },
                {
                    label: 'Currency Risk (20%)',
                    data: chartData.map(c => c.currency),
                    backgroundColor: '#16a34a',
                },
                {
                    label: 'News Risk (20%)',
                    data: chartData.map(c => c.news),
                    backgroundColor: '#f59e0b',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 10 } }
                },
                tooltip: {
                    callbacks: {
                        footer: (tooltipItems) => {
                            const index = tooltipItems[0].dataIndex;
                            return 'Total Index Score: ' + chartData[index].total.toFixed(1);
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { show: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 9 } }
                },
                y: {
                    stacked: true,
                    grid: { color: 'rgba(148, 163, 184, 0.08)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 9 } }
                }
            }
        }
    });
});
</script>
@endpush

@endsection