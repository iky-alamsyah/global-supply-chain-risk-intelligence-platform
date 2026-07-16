<x-guest-layout>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-alert auth-alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-4">
        <h1 class="auth-form-title">Welcome back</h1>
        <p class="auth-form-sub">Sign in to your GSCRIP account to continue.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="you@company.com"
                       required autofocus autocomplete="username">
            </div>
            @error('email')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size:.75rem;">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password"
                       type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Your password"
                       required autocomplete="current-password">
                <button type="button"
                        class="input-group-text"
                        style="cursor:pointer;border-left:0;"
                        onclick="togglePassword('password','toggleIcon')">
                    <i id="toggleIcon" class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="mb-4">
            <div class="form-check">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="form-check-input"
                       style="border-color:rgba(15,23,42,.25);">
                <label class="form-check-label" for="remember_me"
                       style="font-size:.82rem;color:#475569;">
                    Keep me signed in
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth mb-4">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>

        {{-- Register link --}}
        @if (Route::has('register'))
            <p class="text-center mb-0" style="font-size:.82rem;color:#64748B;">
                Don't have an account?
                <a href="{{ route('register') }}" class="auth-link ms-1">Create one free</a>
            </p>
        @endif

    </form>

    <script>
    function togglePassword(id, iconId) {
        const input = document.getElementById(id);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>

</x-guest-layout>
