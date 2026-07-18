@extends('layouts.admin')

@section('title', 'Add Country')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary btn-sm p-1.5" style="border-radius:var(--radius-sm);">
            <i class="bi bi-arrow-left" style="font-size:1rem; line-height:1;"></i>
        </a>
        <div>
            <h1 class="page-header-title">Add New Country</h1>
            <p class="page-header-sub mb-0">Register a new country, populate geographic coordinates, and set active status.</p>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--danger-bg); color:var(--danger); border-left:4px solid var(--danger)!important; border-radius:var(--radius-md);">
        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Please fix the following errors:</h6>
        <ul class="mb-0 ps-3" style="font-size: .82rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Card form --}}
<div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
    <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-plus me-1"></i> Country Registry Details</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.countries.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Country Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Indonesia" required>
                </div>

                {{-- Official Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Official Name</label>
                    <input type="text" name="official_name" value="{{ old('official_name') }}" class="form-control" placeholder="e.g. Republic of Indonesia">
                </div>

                {{-- ISO2 --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ISO2 Code <span class="text-danger">*</span></label>
                    <input type="text" name="iso2" value="{{ old('iso2') }}" class="form-control" placeholder="e.g. ID" minlength="2" maxlength="2" style="text-transform: uppercase;" required>
                </div>

                {{-- ISO3 --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ISO3 Code <span class="text-danger">*</span></label>
                    <input type="text" name="iso3" value="{{ old('iso3') }}" class="form-control" placeholder="e.g. IDN" minlength="3" maxlength="3" style="text-transform: uppercase;" required>
                </div>

                {{-- Region --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Region / Continent <span class="text-danger">*</span></label>
                    <input type="text" name="region" value="{{ old('region') }}" class="form-control" placeholder="e.g. Asia" required>
                </div>

                {{-- Capital --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Capital City</label>
                    <input type="text" name="capital" value="{{ old('capital') }}" class="form-control" placeholder="e.g. Jakarta">
                </div>

                {{-- Population --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Population</label>
                    <input type="number" name="population" value="{{ old('population') }}" class="form-control" placeholder="e.g. 273523615" min="0">
                </div>

                {{-- Latitude --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Latitude</label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="e.g. -6.2088">
                </div>

                {{-- Longitude --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Longitude</label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="e.g. 106.8456">
                </div>

                {{-- GDP --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">GDP (USD)</label>
                    <input type="number" step="any" name="gdp" value="{{ old('gdp') }}" class="form-control" placeholder="e.g. 1186000000000" min="0">
                </div>
            </div>

            <div class="mt-4 pt-2 text-end">
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary btn-sm px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm px-4">Register Country</button>
            </div>
        </form>
    </div>
</div>

@endsection
