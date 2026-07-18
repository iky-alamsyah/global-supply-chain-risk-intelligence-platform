@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-speedometer2 me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Admin Dashboard
        </h1>
        <p class="page-header-sub mb-0">
            Welcome back, <strong>{{ auth()->user()->name }}</strong> — GSCRIP platform administrator overview.
        </p>
    </div>
</div>

{{-- Summary Cards Row 1 --}}
<div class="row g-3 mb-3">

    {{-- Users --}}
    <div class="col-lg-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold" style="font-size: .8rem; text-transform: uppercase; letter-spacing: .05em;">Total Users</span>
                    <h2 class="mt-2 mb-0 fw-bold text-dark" style="font-size: 2.2rem; line-height: 1;">{{ $totalUsers }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 54px; height: 54px; border-radius: var(--radius-md); background: rgba(37,99,235,.08); color: var(--primary-light);">
                    <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Countries --}}
    <div class="col-lg-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold" style="font-size: .8rem; text-transform: uppercase; letter-spacing: .05em;">Total Countries</span>
                    <h2 class="mt-2 mb-0 fw-bold text-dark" style="font-size: 2.2rem; line-height: 1;">{{ $totalCountries }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 54px; height: 54px; border-radius: var(--radius-md); background: rgba(22,163,74,.08); color: var(--success);">
                    <i class="bi bi-globe2" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Ports --}}
    <div class="col-lg-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold" style="font-size: .8rem; text-transform: uppercase; letter-spacing: .05em;">Total Ports</span>
                    <h2 class="mt-2 mb-0 fw-bold text-dark" style="font-size: 2.2rem; line-height: 1;">{{ $totalPorts }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 54px; height: 54px; border-radius: var(--radius-md); background: rgba(217,119,6,.08); color: var(--warning);">
                    <i class="bi bi-water" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Favorites --}}
    <div class="col-lg-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold" style="font-size: .8rem; text-transform: uppercase; letter-spacing: .05em;">Total Favorites</span>
                    <h2 class="mt-2 mb-0 fw-bold text-dark" style="font-size: 2.2rem; line-height: 1;">{{ $totalFavorites }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 54px; height: 54px; border-radius: var(--radius-md); background: rgba(245,158,11,.08); color: #fbbf24;">
                    <i class="bi bi-star-fill" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Summary Cards Row 2 (Article Breakdowns) --}}
<div class="row g-3 mb-4">

    {{-- Total Articles --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between" style="border-radius: var(--radius-md); background: var(--surface);">
            <div>
                <span class="text-muted small fw-semibold text-uppercase" style="font-size: .72rem; letter-spacing: .05em;">Total Articles</span>
                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalArticles }}</h4>
            </div>
            <i class="bi bi-file-earmark-richtext text-danger" style="font-size: 1.6rem; opacity: .75;"></i>
        </div>
    </div>

    {{-- Published Articles --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between" style="border-radius: var(--radius-md); background: var(--surface);">
            <div>
                <span class="text-muted small fw-semibold text-uppercase" style="font-size: .72rem; letter-spacing: .05em;">Published Articles</span>
                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $publishedArticles }}</h4>
            </div>
            <i class="bi bi-globe text-success" style="font-size: 1.6rem; opacity: .75;"></i>
        </div>
    </div>

    {{-- Draft Articles --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between" style="border-radius: var(--radius-md); background: var(--surface);">
            <div>
                <span class="text-muted small fw-semibold text-uppercase" style="font-size: .72rem; letter-spacing: .05em;">Draft Articles</span>
                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $draftArticles }}</h4>
            </div>
            <i class="bi bi-pencil-square text-warning" style="font-size: 1.6rem; opacity: .75;"></i>
        </div>
    </div>

    {{-- Archived Articles --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between" style="border-radius: var(--radius-md); background: var(--surface);">
            <div>
                <span class="text-muted small fw-semibold text-uppercase" style="font-size: .72rem; letter-spacing: .05em;">Archived Articles</span>
                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $archivedArticles }}</h4>
            </div>
            <i class="bi bi-archive text-secondary" style="font-size: 1.6rem; opacity: .75;"></i>
        </div>
    </div>

</div>

{{-- Layout: Chart + System Summary --}}
<div class="row g-4 mb-4">
    {{-- Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line-fill me-1 text-primary"></i> Artikel Analisis by Category</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="position: relative; height:240px; width:100%;">
                    <canvas id="articleCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- System Status --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu me-1 text-success"></i> System Summary</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">Risk Distribution High</span>
                        <span class="badge bg-danger rounded-pill">{{ $highRisk }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">Risk Distribution Medium</span>
                        <span class="badge bg-warning text-dark rounded-pill">{{ $mediumRisk }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">Risk Distribution Low</span>
                        <span class="badge bg-success rounded-pill">{{ $lowRisk }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Environment</span>
                        <span class="fw-semibold text-success small">Production</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Load Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('articleCategoryChart').getContext('2d');
    const chartData = @json($articleChartData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(chartData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                label: 'Articles Count',
                data: Object.values(chartData),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.75)', // economy
                    'rgba(16, 185, 129, 0.75)', // trade
                    'rgba(245, 158, 11, 0.75)', // shipping
                    'rgba(220, 38, 38, 0.75)'   // logistics
                ],
                borderColor: [
                    '#2563eb',
                    '#10b981',
                    '#d97706',
                    '#dc2626'
                ],
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#64748b',
                        font: {
                            family: 'Inter, sans-serif'
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Inter, sans-serif'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush