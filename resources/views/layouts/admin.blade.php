<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GSCRIP Admin — Global Supply Chain Risk Intelligence Platform">

    <title>@yield('title', 'GSCRIP Admin') — GSCRIP Admin</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Leaflet JS & CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- App CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/dashboard.css',
        'resources/js/app.js'
    ])

    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body>

<div class="app-wrapper">

    {{-- Admin Sidebar --}}
    @include('components.admin-sidebar')

    {{-- Sidebar overlay (mobile) --}}
    <div class="sidebar-overlay" id="adminSidebarOverlay"></div>

    <div class="main-content">

        {{-- Admin Navbar --}}
        @include('components.admin-navbar')

        <main class="content-wrapper">
            @yield('content')
        </main>

    </div>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Sidebar Toggle (Mobile) --}}
<script>
(function () {
    const toggle  = document.getElementById('adminSidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('adminSidebarOverlay');

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
})();
</script>

<style>
.sidebar-overlay {
    display    : none;
    position   : fixed;
    inset      : 0;
    background : rgba(15, 23, 42, 0.5);
    z-index    : 1039;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.active { display: block; }
</style>

@stack('scripts')

</body>

</html>