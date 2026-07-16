<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GSCRIP') }} — Global Supply Chain Risk Intelligence</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary      : #1E3A8A;
            --primary-light: #2563EB;
            --info         : #0891B2;
            --text         : #1E293B;
            --text-muted   : #64748B;
            --border       : rgba(15,23,42,.1);
            --surface      : #FFFFFF;
            --radius-md    : 12px;
            --radius-lg    : 16px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin      : 0;
            font-family : 'Inter', -apple-system, sans-serif;
            font-size   : 14px;
            background  : #F1F5F9;
            color       : var(--text);
            -webkit-font-smoothing: antialiased;
            min-height  : 100vh;
        }

        /* ── Auth Split Layout ─────────────────────────────── */
        .auth-wrapper {
            display    : flex;
            min-height : 100vh;
        }

        /* Left panel — branding */
        .auth-panel {
            width      : 45%;
            background : linear-gradient(160deg, #0F172A 0%, #1E3A8A 50%, #0891B2 100%);
            padding    : 48px;
            display    : flex;
            flex-direction: column;
            justify-content: space-between;
            position   : relative;
            overflow   : hidden;
            flex-shrink: 0;
        }

        .auth-panel::before {
            content    : '';
            position   : absolute;
            inset      : 0;
            background : url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .auth-panel-brand {
            position : relative;
            z-index  : 1;
        }

        .auth-brand-icon {
            width           : 56px;
            height          : 56px;
            background      : rgba(255,255,255,.15);
            border-radius   : 14px;
            display         : flex;
            align-items     : center;
            justify-content : center;
            font-size       : 1.6rem;
            color           : #fff;
            margin-bottom   : 20px;
            border          : 1px solid rgba(255,255,255,.2);
            backdrop-filter : blur(8px);
        }

        .auth-brand-name {
            font-size     : 1.6rem;
            font-weight   : 800;
            color         : #fff;
            letter-spacing: -.03em;
            margin-bottom : 6px;
        }

        .auth-brand-desc {
            font-size  : .85rem;
            color      : rgba(255,255,255,.65);
            line-height: 1.6;
            max-width  : 280px;
        }

        .auth-panel-stats {
            position : relative;
            z-index  : 1;
        }

        .auth-stat-grid {
            display               : grid;
            grid-template-columns : repeat(2, 1fr);
            gap                   : 14px;
        }

        .auth-stat-item {
            background    : rgba(255,255,255,.08);
            border-radius : var(--radius-md);
            padding       : 14px;
            border        : 1px solid rgba(255,255,255,.12);
        }

        .auth-stat-num {
            font-size   : 1.5rem;
            font-weight : 800;
            color       : #fff;
            line-height : 1;
        }

        .auth-stat-lbl {
            font-size  : .72rem;
            color      : rgba(255,255,255,.55);
            margin-top : 4px;
            font-weight: 500;
        }

        /* Right panel — form */
        .auth-form-panel {
            flex            : 1;
            display         : flex;
            align-items     : center;
            justify-content : center;
            padding         : 40px 32px;
            background      : var(--surface);
        }

        .auth-form-inner {
            width     : 100%;
            max-width : 400px;
        }

        .auth-form-title {
            font-size     : 1.5rem;
            font-weight   : 800;
            color         : var(--text);
            margin-bottom : 4px;
            letter-spacing: -.02em;
        }

        .auth-form-sub {
            font-size     : .85rem;
            color         : var(--text-muted);
            margin-bottom : 28px;
        }

        /* Form fields */
        .auth-form .form-control {
            border        : 1px solid rgba(15,23,42,.14) !important;
            border-radius : var(--radius-md) !important;
            padding       : 11px 14px !important;
            font-size     : .88rem !important;
            color         : var(--text) !important;
            background    : #FAFBFC !important;
            transition    : all .2s !important;
        }

        .auth-form .form-control:focus {
            border-color : var(--primary-light) !important;
            box-shadow   : 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            background   : #fff !important;
        }

        .auth-form .form-label {
            font-size   : .78rem;
            font-weight : 600;
            color       : #334155;
            margin-bottom: 6px;
        }

        .auth-form .input-group-text {
            background    : #F1F5F9 !important;
            border        : 1px solid rgba(15,23,42,.14) !important;
            border-radius : var(--radius-md) !important;
            color         : var(--text-muted) !important;
        }

        .auth-form .input-group .form-control {
            border-radius : 0 var(--radius-md) var(--radius-md) 0 !important;
        }

        .auth-form .input-group .input-group-text:first-child {
            border-radius : var(--radius-md) 0 0 var(--radius-md) !important;
        }

        /* Submit button */
        .btn-auth {
            width           : 100%;
            padding         : 12px 20px !important;
            background      : linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
            border           : none !important;
            border-radius   : var(--radius-md) !important;
            color           : #fff !important;
            font-size       : .9rem !important;
            font-weight     : 700 !important;
            letter-spacing  : -.01em;
            cursor          : pointer;
            transition      : all .25s !important;
            box-shadow      : 0 4px 16px rgba(30, 58, 138, 0.3) !important;
        }

        .btn-auth:hover {
            transform  : translateY(-2px);
            box-shadow : 0 8px 24px rgba(30, 58, 138, 0.4) !important;
        }

        .btn-auth:active { transform: translateY(0); }

        /* Links */
        .auth-link {
            color           : var(--primary-light);
            font-weight     : 600;
            text-decoration : none;
        }
        .auth-link:hover { text-decoration: underline; }

        /* Error */
        .auth-error {
            font-size  : .75rem;
            color      : #DC2626;
            margin-top : 4px;
        }

        /* Alert */
        .auth-alert {
            padding       : 10px 14px;
            border-radius : var(--radius-md);
            font-size     : .82rem;
            margin-bottom : 16px;
        }

        .auth-alert-success {
            background : rgba(22,163,74,.08);
            color      : #15803D;
            border     : 1px solid rgba(22,163,74,.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-panel { display: none; }
            .auth-form-panel { padding: 24px 20px; }
        }
    </style>
</head>

<body>
<div class="auth-wrapper">

    {{-- Left: Branding Panel --}}
    <div class="auth-panel d-none d-md-flex flex-column justify-content-between">

        <div class="auth-panel-brand">
            <div class="auth-brand-icon">
                <i class="bi bi-globe2"></i>
            </div>
            <div class="auth-brand-name">GSCRIP</div>
            <p class="auth-brand-desc">
                Global Supply Chain Risk Intelligence Platform — Real-time monitoring, risk analytics, and intelligence for global trade networks.
            </p>
        </div>

        <div class="auth-panel-stats">
            <div class="auth-stat-grid">
                <div class="auth-stat-item">
                    <div class="auth-stat-num">195+</div>
                    <div class="auth-stat-lbl">Countries Monitored</div>
                </div>
                <div class="auth-stat-item">
                    <div class="auth-stat-num">2.5K+</div>
                    <div class="auth-stat-lbl">Global Ports</div>
                </div>
                <div class="auth-stat-item">
                    <div class="auth-stat-num">24/7</div>
                    <div class="auth-stat-lbl">Live Monitoring</div>
                </div>
                <div class="auth-stat-item">
                    <div class="auth-stat-num">AI</div>
                    <div class="auth-stat-lbl">Risk Engine</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Right: Form Panel --}}
    <div class="auth-form-panel">
        <div class="auth-form-inner">
            {{ $slot }}
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
