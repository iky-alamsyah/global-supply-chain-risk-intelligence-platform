<nav class="top-navbar">

    {{-- Mobile Sidebar Toggle --}}
    <button class="sidebar-toggle" id="sidebarToggle" type="button" title="Toggle Sidebar">
        <i class="bi bi-list" style="font-size:1.2rem;"></i>
    </button>

    {{-- Breadcrumb / Page Title --}}
    <div class="navbar-breadcrumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;font-size:.8rem;">
                        <i class="bi bi-house-door"></i>
                    </a>
                </li>
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @else
                    <li class="breadcrumb-item active" aria-current="page"
                        style="font-size:.8rem;font-weight:600;color:var(--text);">
                        @yield('title', 'Dashboard')
                    </li>
                @endif
            </ol>
        </nav>
    </div>

    {{-- Right Controls --}}
    <div class="navbar-right">

        {{-- Search (compact) --}}
        <div class="d-none d-md-flex align-items-center" style="position:relative;">
            <i class="bi bi-search"
               style="position:absolute;left:10px;font-size:.8rem;color:var(--text-subtle);pointer-events:none;"></i>
            <input type="text"
                   placeholder="Quick search..."
                   style="padding:6px 12px 6px 30px;border:1px solid var(--border);border-radius:var(--radius-sm);
                          font-size:.78rem;color:var(--text);background:var(--surface-alt);width:180px;
                          outline:none;transition:all .2s;"
                   onfocus="this.style.width='220px';this.style.borderColor='var(--primary-light)'"
                   onblur="this.style.width='180px';this.style.borderColor='var(--border)'">
        </div>

        <div class="navbar-sep d-none d-md-block"></div>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <div class="navbar-user" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                <div class="navbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-sm-block">
                    <div class="navbar-user-name">{{ auth()->user()->name ?? 'User' }}</div>
                    <span class="navbar-user-role">Analyst</span>
                </div>
                <i class="bi bi-chevron-down d-none d-sm-block"
                   style="font-size:.65rem;color:var(--text-subtle);margin-left:2px;"></i>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-1"
                style="min-width:200px;border-radius:var(--radius-md);border:1px solid var(--border)!important;
                       box-shadow:var(--shadow-lg)!important;font-size:.82rem;">

                <li>
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);">
                        <div style="font-weight:700;color:var(--text);">{{ auth()->user()->name ?? 'User' }}</div>
                        <div style="font-size:.72rem;color:var(--text-muted);">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#"
                       style="color:var(--text-secondary);">
                        <i class="bi bi-person-circle" style="color:var(--text-muted);width:16px;"></i>
                        My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#"
                       style="color:var(--text-secondary);">
                        <i class="bi bi-gear" style="color:var(--text-muted);width:16px;"></i>
                        Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1" style="border-color:var(--border);"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="dropdown-item d-flex align-items-center gap-2 py-2"
                                style="color:var(--danger);font-weight:600;">
                            <i class="bi bi-box-arrow-right" style="width:16px;"></i>
                            Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>

</nav>