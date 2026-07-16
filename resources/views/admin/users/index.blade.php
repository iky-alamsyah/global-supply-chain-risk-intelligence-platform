@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-people-fill me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            User Management
        </h1>
        <p class="page-header-sub mb-0">Manage system users, roles, account status, and passwords.</p>
    </div>
    <div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg"></i> Create User
        </button>
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background:var(--danger-bg); color:var(--danger); border-left:4px solid var(--danger)!important; border-radius:var(--radius-md);">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
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

{{-- ── FILTER BAR ───────────────────────────────────────── --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-md-5">
            <label class="form-label">Search User</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Search by name or email...">
                @if($search)
                    <a href="{{ route('admin.users.index') }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);text-decoration:none;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Role Filter --}}
        <div class="col-md-2">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="admin" @selected($roleFilter == 'admin')>Admin</option>
                <option value="user" @selected($roleFilter == 'user')>User</option>
            </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" @selected($statusFilter == 'active')>Active</option>
                <option value="inactive" @selected($statusFilter == 'inactive')>Inactive</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if($search || $roleFilter || $statusFilter)
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- ── USERS TABLE ───────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:var(--primary-50);color:var(--primary-light);">
            <i class="bi bi-table"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">User Records</span>
        <span class="ms-2 badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $users->total() }} total
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="width:150px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $u)
                        <tr>
                            <td style="color:var(--text-subtle);font-size:.72rem;font-weight:600;">
                                {{ $users->firstItem() + $i }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="navbar-avatar" style="background: {{ $u->role === 'admin' ? 'var(--danger)' : 'var(--primary-light)' }}; color: white; width: 32px; height: 32px; font-size: .8rem; font-weight: 700;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:.85rem;color:var(--text);">
                                            {{ $u->name }}
                                            @if($u->id === auth()->id())
                                                <span class="badge bg-secondary ms-1" style="font-size:.6rem;">You</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:.82rem;color:var(--text-secondary);">{{ $u->email }}</span>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger" style="font-size: .7rem; border-radius: 99px; padding: .25rem .6rem;">
                                        <i class="bi bi-shield-lock-fill me-1"></i>ADMIN
                                    </span>
                                @else
                                    <span class="badge bg-primary" style="font-size: .7rem; border-radius: 99px; padding: .25rem .6rem;">
                                        <i class="bi bi-person me-1"></i>USER
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($u->status === 'active')
                                    <span class="badge bg-success" style="font-size: .7rem; border-radius: 99px; padding: .25rem .6rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>ACTIVE
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: .7rem; border-radius: 99px; padding: .25rem .6rem;">
                                        <i class="bi bi-x-circle me-1"></i>INACTIVE
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:.78rem;color:var(--text-muted);">
                                    {{ $u->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <button class="btn btn-sm btn-outline-primary edit-user-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-email="{{ $u->email }}"
                                            data-role="{{ $u->role }}"
                                            data-status="{{ $u->status }}"
                                            title="Edit User">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <button class="btn btn-sm btn-outline-danger delete-user-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteUserModal"
                                                data-id="{{ $u->id }}"
                                                data-name="{{ $u->name }}"
                                                title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="You cannot delete yourself">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <div class="mb-3 d-inline-flex align-items-center justify-content-center text-muted" 
                                     style="width: 60px; height: 60px; border-radius: 50%; background: var(--surface-alt); border: 1px solid var(--border);">
                                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                                </div>
                                <h6 style="color:var(--text-secondary);font-weight:600;">No users found</h6>
                                <p style="font-size:.82rem;color:var(--text-muted);max-width:320px;margin:0 auto 16px;">
                                    Try adjusting your filters or search keywords.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>

{{-- ── CREATE USER MODAL ────────────────────────────────────── --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="createUserModalLabel"><i class="bi bi-person-plus me-1"></i> Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. john@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── EDIT USER MODAL ──────────────────────────────────────── --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="editUserModalLabel"><i class="bi bi-pencil-square me-1"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password (Optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span id="selfRoleAlert" class="text-danger d-none" style="font-size: .72rem; display:block; margin-top: 4px;">
                                You cannot change your own admin role.
                            </span>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span id="selfStatusAlert" class="text-danger d-none" style="font-size: .72rem; display:block; margin-top: 4px;">
                                You cannot deactivate yourself.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── DELETE USER MODAL ────────────────────────────────────── --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="deleteUserModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center text-danger" 
                     style="width: 54px; height: 54px; border-radius: 50%; background: rgba(220,38,38,.08);">
                    <i class="bi bi-trash" style="font-size: 1.6rem;"></i>
                </div>
                <p class="mb-0 text-secondary" style="font-size: .9rem;">
                    Are you sure you want to delete user <strong id="delete_user_name_text" class="text-dark"></strong>?
                </p>
                <p class="text-muted mt-1 mb-0" style="font-size: .78rem;">
                    This action cannot be undone and will remove all related user favorites.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteUserForm" method="POST" class="flex-fill m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentUserId = {{ auth()->id() }};

    // Edit User Modal Handling
    const editBtns = document.querySelectorAll(".edit-user-btn");
    const editForm = document.getElementById("editUserForm");
    const editName = document.getElementById("edit_name");
    const editEmail = document.getElementById("edit_email");
    const editRole = document.getElementById("edit_role");
    const editStatus = document.getElementById("edit_status");
    const selfRoleAlert = document.getElementById("selfRoleAlert");
    const selfStatusAlert = document.getElementById("selfStatusAlert");

    editBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");
            const email = this.getAttribute("data-email");
            const role = this.getAttribute("data-role");
            const status = this.getAttribute("data-status");

            editForm.action = `/admin/users/${id}`;
            editName.value = name;
            editEmail.value = email;
            editRole.value = role;
            editStatus.value = status;

            // Security locks if editing oneself
            if (parseInt(id) === currentUserId) {
                editRole.disabled = true;
                editStatus.disabled = true;
                selfRoleAlert.classList.remove("d-none");
                selfStatusAlert.classList.remove("d-none");
            } else {
                editRole.disabled = false;
                editStatus.disabled = false;
                selfRoleAlert.classList.add("d-none");
                selfStatusAlert.classList.add("d-none");
            }
        });
    });

    // Handle submit when select is disabled (disabled inputs are not sent in form submit)
    editForm.addEventListener("submit", function () {
        editRole.disabled = false;
        editStatus.disabled = false;
    });

    // Delete User Modal Handling
    const deleteBtns = document.querySelectorAll(".delete-user-btn");
    const deleteForm = document.getElementById("deleteUserForm");
    const deleteText = document.getElementById("delete_user_name_text");

    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");

            deleteForm.action = `/admin/users/${id}`;
            deleteText.textContent = name;
        });
    });
});
</script>
@endpush
