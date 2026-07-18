@extends('layouts.admin')

@section('title', 'Add Port')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary btn-sm p-1.5" style="border-radius:var(--radius-sm);">
            <i class="bi bi-arrow-left" style="font-size:1rem; line-height:1;"></i>
        </a>
        <div>
            <h1 class="page-header-title">Add New Port</h1>
            <p class="page-header-sub mb-0">Register a new port, location coordinates, and metadata.</p>
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

{{-- Form Card --}}
<div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
    <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-plus me-1"></i> Port Registry Specifications</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.ports.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Country --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                    <select name="country_id" class="form-select" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" @selected(old('country_id') == $c->id)>
                                {{ $c->flag }} {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Port Name --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Port Name <span class="text-danger">*</span></label>
                    <input type="text" name="port_name" value="{{ old('port_name') }}" class="form-control" placeholder="e.g. Port of Jakarta" required>
                </div>

                {{-- Port Code --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Port Code / LOCODE</label>
                    <input type="text" name="port_code" value="{{ old('port_code') }}" class="form-control" placeholder="e.g. IDJKT">
                </div>

                {{-- City --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="e.g. North Jakarta">
                </div>

                {{-- Timezone --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', 'Asia/Jakarta') }}" class="form-control" placeholder="e.g. Asia/Jakarta">
                </div>

                {{-- Latitude --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Latitude <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="e.g. -6.10305" required>
                </div>

                {{-- Longitude --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Longitude <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="e.g. 106.8788" required>
                </div>

                {{-- Status --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status') == 'active')>Active</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Enter port description, capacity, and risk profiles...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-4 pt-2 text-end">
                <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary btn-sm px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm px-4">Save Port</button>
            </div>
        </form>
    </div>
</div>

@endsection
