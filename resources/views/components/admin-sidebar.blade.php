<aside class="sidebar" id="adminSidebar">

    {{-- Brand --}}
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" style="text-decoration:none;">
        <div class="brand-icon" style="background: linear-gradient(135deg, #DC2626, #B91C1C);">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="brand-text">
            <div class="brand-title">GSCRIP ADMIN</div>
            <span class="brand-subtitle">Control Panel</span>
        </div>
    </a>

    {{-- Menu --}}
    <nav class="sidebar-menu" id="adminSidebarMenu">

        {{-- Core --}}
        <div class="sidebar-label">Core</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        {{-- Management --}}
        <div class="sidebar-label" style="margin-top:8px;">Management</div>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>User Management</span>
        </a>

        <a href="#" class="sidebar-link">
            <i class="bi bi-globe-americas"></i>
            <span>Country Management</span>
        </a>

        <a href="#" class="sidebar-link">
            <i class="bi bi-anchor"></i>
            <span>Port Management</span>
        </a>

        <a href="#" class="sidebar-link">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Article Management</span>
        </a>

        {{-- Monitoring --}}
        <div class="sidebar-label" style="margin-top:8px;">Monitoring</div>

        <a href="#" class="sidebar-link">
            <i class="bi bi-activity"></i>
            <span>Risk Monitoring</span>
        </a>

        <div class="sidebar-sep"></div>

        {{-- Settings --}}
        <a href="#" class="sidebar-link">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-footer-user">
            <div class="sidebar-footer-avatar" style="background: var(--danger); color: white;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div class="sidebar-footer-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <span class="sidebar-footer-role">Administrator</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="flex-shrink:0;">
                @csrf
                <button type="submit"
                        class="sidebar-link"
                        style="background:none;border:none;padding:5px;cursor:pointer;opacity:.6;"
                        title="Logout">
                    <i class="bi bi-box-arrow-right text-danger"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
