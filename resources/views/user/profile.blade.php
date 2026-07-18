@extends('layouts.dashboard')

@section('title', '👤 My Profile')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-person-circle me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            My Profile
        </h1>
        <p class="page-header-sub mb-0">View and update your personal account information, avatar, and password.</p>
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

<div class="row g-4">

    {{-- ── LEFT PANEL: PROFILE CARD ──────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="position-relative d-inline-block mx-auto mb-3">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle border"
                         style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white fw-bold shadow-sm" 
                         style="width: 120px; height: 120px; font-size: 3rem; background: var(--primary-light);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
            <p class="text-muted mb-3" style="font-size: .85rem;">{{ $user->email }}</p>

            <span class="badge bg-primary mb-4" style="font-size: .75rem; border-radius: 99px; padding: .35rem .8rem;">
                <i class="bi bi-person-fill me-1"></i>{{ strtoupper($user->role) }}
            </span>

            <div class="border-top pt-3 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size: .8rem;">Joined Date</span>
                    <strong class="text-dark" style="font-size: .8rem;">{{ $user->created_at->format('M d, Y') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size: .8rem;">Status</span>
                    <strong class="text-success" style="font-size: .8rem;"><i class="bi bi-check-circle-fill me-1"></i>Active</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL: EDIT FORMS ───────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-gear me-1"></i> Edit Account Profile</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <span class="text-muted" style="font-size: .72rem;">Supported formats: JPG, PNG, GIF. Max file size: 2MB.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
