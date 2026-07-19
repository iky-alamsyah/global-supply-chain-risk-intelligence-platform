<aside class="sidebar" id="appSidebar">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand" style="text-decoration:none;">
        <div class="brand-icon">
            <i class="bi bi-globe2"></i>
        </div>
        <div class="brand-text">
            <div class="brand-title">GSCRIP</div>
            <span class="brand-subtitle">Risk Intelligence</span>
        </div>
    </a>

    {{-- Menu --}}
    <nav class="sidebar-menu" id="sidebarMenu">

        {{-- Main --}}
        <div class="sidebar-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        {{-- Intelligence --}}
        <div class="sidebar-label" style="margin-top:8px;">Intelligence</div>

        <a href="{{ route('countries.index') }}"
           class="sidebar-link {{ request()->routeIs('countries.*') ? 'active' : '' }}">
            <i class="bi bi-globe-americas"></i>
            <span>Global Countries</span>
        </a>

        <a href="{{ route('news.index') }}"
           class="sidebar-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i>
            <span>News Intelligence</span>
            <span class="badge-live">Live</span>
        </a>

        <a href="{{ route('ports.index') }}"
           class="sidebar-link {{ request()->routeIs('ports.*') ? 'active' : '' }}">
            <i class="bi bi-water"></i>
            <span>Port Dashboard</span>
        </a>

        {{-- Analytics --}}
        <div class="sidebar-label" style="margin-top:8px;">Analytics</div>

        <a href="{{ route('visualization.index') }}"
           class="sidebar-link {{ request()->routeIs('visualization.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>Visualization</span>
        </a>

        <a href="{{ route('risk-engine.index') }}"
           class="sidebar-link {{ request()->routeIs('risk-engine.*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            <span>Risk Engine</span>
        </a>

        <a href="{{ route('weather.index') }}"
           class="sidebar-link {{ request()->routeIs('weather.*') ? 'active' : '' }}">
            <i class="bi bi-cloud-sun"></i>
            <span>Weather</span>
        </a>

        <a href="{{ route('currency.index') }}"
           class="sidebar-link {{ request()->routeIs('currency.*') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i>
            <span>Currency</span>
        </a>

        <a href="{{ route('comparison.index') }}"
           class="sidebar-link {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
            <i class="bi bi-intersect"></i>
            <span>Comparison</span>
        </a>

        <a href="{{ route('shipment-estimation.index') }}"
           class="sidebar-link {{ request()->routeIs('shipment-estimation.*') ? 'active' : '' }}">
            <i class="bi bi-compass"></i>
            <span>Shipment Route Estimation</span>
        </a>

        <a href="{{ route('favorites.index') }}"
           class="sidebar-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
            <i class="bi bi-star-fill"></i>
            <span>Favorites</span>
        </a>

        <div class="sidebar-sep"></div>

        <a href="{{ route('profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-footer-user">
            @if(auth()->user()->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                     alt="{{ auth()->user()->name }}" 
                     class="rounded-circle border"
                     style="width: 32px; height: 32px; object-fit: cover; margin-right: 8px;">
            @else
                <div class="sidebar-footer-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div style="flex:1;min-width:0;">
                <div class="sidebar-footer-name">{{ auth()->user()->name ?? 'User' }}</div>
                <span class="sidebar-footer-role">Analyst</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="flex-shrink:0;">
                @csrf
                <button type="submit"
                        class="sidebar-link"
                        style="background:none;border:none;padding:5px;cursor:pointer;opacity:.6;"
                        title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</aside>