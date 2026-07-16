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

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

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
                    <i class="bi bi-anchor" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Articles --}}
    <div class="col-lg-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold" style="font-size: .8rem; text-transform: uppercase; letter-spacing: .05em;">Total Articles</span>
                    <h2 class="mt-2 mb-0 fw-bold text-dark" style="font-size: 2.2rem; line-height: 1;">{{ $totalArticles }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 54px; height: 54px; border-radius: var(--radius-md); background: rgba(220,38,38,.08); color: var(--danger);">
                    <i class="bi bi-file-earmark-text-fill" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Welcome Admin Panel --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: var(--radius-md); background: var(--surface);">
    <div class="card-body p-4">
        <h4 class="fw-bold mb-2">Welcome to the Administrator Panel</h4>
        <p class="text-secondary mb-0" style="max-width: 600px;">
            Here you can monitor core metrics, manage active assets, adjust global risk scoring variables, and view system logs. Select an option from the sidebar to begin.
        </p>
    </div>
</div>

@endsection