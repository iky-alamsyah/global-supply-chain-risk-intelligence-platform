@extends('layouts.admin')

@section('title', 'Dataset Pelabuhan')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-hdd-stack me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Dataset Pelabuhan
        </h1>
        <p class="page-header-sub mb-0">Manage global maritime trade ports registry, location coordinates, and operational status.</p>
    </div>
    <div>
        <a href="{{ route('admin.ports.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> Add Port
        </a>
    </div>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--success-bg); color:var(--success); border-left:4px solid var(--success)!important; border-radius:var(--radius-md);">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Statistics Row --}}
<div class="row g-3 mb-4">
    {{-- Total Ports --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,.1);">
                <i class="bi bi-anchor" style="font-size: 1.4rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .7rem; letter-spacing: .05em;">Total Pelabuhan</div>
                <h4 class="fw-bold text-dark mb-0">{{ number_format($totalPorts) }}</h4>
            </div>
        </div>
    </div>

    {{-- Active Ports --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,.1);">
                <i class="bi bi-check-circle" style="font-size: 1.4rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .7rem; letter-spacing: .05em;">Active</div>
                <h4 class="fw-bold text-dark mb-0">{{ number_format($activePorts) }}</h4>
            </div>
        </div>
    </div>

    {{-- Inactive Ports --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center gap-3" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-secondary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(107,114,128,.1);">
                <i class="bi bi-slash-circle" style="font-size: 1.4rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .7rem; letter-spacing: .05em;">Inactive</div>
                <h4 class="fw-bold text-dark mb-0">{{ number_format($inactivePorts) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Filter & Search Form --}}
<form method="GET" id="searchFilterForm" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search Input --}}
        <div class="col-md-4">
            <label class="form-label">Search Ports</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       id="searchInput"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Name, city, country, code..."
                       autocomplete="off">
            </div>
        </div>

        {{-- Country Filter --}}
        <div class="col-md-3">
            <label class="form-label">Country</label>
            <select name="country_id" id="countryFilter" class="form-select">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" @selected($countryId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active" @selected($statusFilter == 'active')>Active</option>
                <option value="inactive" @selected($statusFilter == 'inactive')>Inactive</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if($search || $countryId || $statusFilter)
                <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Ports Registry</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $ports->total() }} records
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Port Name</th>
                        <th>Port Code</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Status</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $index => $port)
                        <tr>
                            <td>{{ $ports->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.ports.show', $port) }}" style="font-weight:700; color:var(--text); text-decoration:none;" class="hover-link">
                                    {{ $port->port_name }}
                                </a>
                            </td>
                            <td style="font-family: monospace;">{{ $port->port_code ?: '—' }}</td>
                            <td>
                                @if($port->country)
                                    <span style="font-size: .85rem;">{{ $port->country->flag }}</span>
                                    <span>{{ $port->country->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $port->city ?: '—' }}</td>
                            <td>{{ number_format((float)$port->latitude, 5) }}</td>
                            <td>{{ number_format((float)$port->longitude, 5) }}</td>
                            <td>
                                @if(strtoupper(trim($port->status)) === 'ACTIVE')
                                    <span class="badge bg-success" style="font-size:.7rem; padding:.25rem .55rem; border-radius:99px;">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-light" style="font-size:.7rem; padding:.25rem .55rem; border-radius:99px;">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <a href="{{ route('admin.ports.show', $port) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deletePortModal"
                                            data-id="{{ $port->id }}"
                                            data-name="{{ $port->port_name }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="bi bi-anchor d-block mb-2" style="font-size: 2rem; opacity: .3;"></i>
                                <strong>No ports found matching your filters.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $ports->links() }}
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deletePortModal" tabindex="-1" aria-labelledby="deletePortModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="deletePortModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete Port</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center text-danger" 
                     style="width: 54px; height: 54px; border-radius: 50%; background: rgba(220,38,38,.08);">
                    <i class="bi bi-trash" style="font-size: 1.6rem;"></i>
                </div>
                <p class="mb-0 text-secondary" style="font-size: .9rem;">
                    Are you sure you want to delete <strong id="delete_port_name" class="text-dark"></strong> from registry?
                </p>
                <p class="text-muted mt-1 mb-0" style="font-size: .78rem;">
                    This action is permanent and cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePortForm" method="POST" class="flex-fill m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteBtns = document.querySelectorAll(".delete-btn");
    const deleteForm = document.getElementById("deletePortForm");
    const deleteName = document.getElementById("delete_port_name");

    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");

            deleteForm.action = `/admin/ports/${id}`;
            deleteName.textContent = name;
        });
    });

    // Realtime Search Debounce
    const searchForm = document.getElementById("searchFilterForm");
    const searchInput = document.getElementById("searchInput");
    const countryFilter = document.getElementById("countryFilter");
    const statusFilter = document.getElementById("statusFilter");

    let timeout = null;
    searchInput.addEventListener("keyup", function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchForm.submit();
        }, 500); // 500ms debounce
    });

    countryFilter.addEventListener("change", function () {
        searchForm.submit();
    });

    statusFilter.addEventListener("change", function () {
        searchForm.submit();
    });
});
</script>
@endpush
