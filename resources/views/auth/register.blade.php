<x-guest-layout>

    {{-- Header --}}
    <div class="mb-4">
        <h1 class="auth-form-title">Create your account</h1>
        <p class="auth-form-sub">Join GSCRIP to access global supply chain risk intelligence.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input id="name"
                       type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="John Doe"
                       required autofocus autocomplete="name">
            </div>
            @error('name')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

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
                       required autocomplete="username">
            </div>
            @error('email')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password"
                       type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Min 8 characters"
                       required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <button type="button" class="input-group-text" style="cursor:pointer;border-left:0;"
                        onclick="togglePassword('password','pwdIcon')">
                    <i id="pwdIcon" class="bi bi-eye"></i>
                </button>
            </div>
            {{-- Strength bar --}}
            <div class="mt-2" id="strengthWrap" style="display:none;">
                <div style="height:4px;border-radius:99px;background:#E2E8F0;overflow:hidden;">
                    <div id="strengthBar" style="height:100%;width:0;border-radius:99px;transition:width .3s,background .3s;"></div>
                </div>
                <div id="strengthText" style="font-size:.7rem;margin-top:3px;color:#64748B;"></div>
            </div>
            @error('password')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="Repeat your password"
                       required autocomplete="new-password">
            </div>
            @error('password_confirmation')
                <div class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth mb-4">
            <i class="bi bi-person-plus me-2"></i>Create Account
        </button>

        {{-- Login link --}}
        <p class="text-center mb-0" style="font-size:.82rem;color:#64748B;">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link ms-1">Sign in</a>
        </p>

    </form>

    <script>
    function togglePassword(id, iconId) {
        const input = document.getElementById(id);
        const icon  = document.getElementById(iconId);
        input.type  = (input.type === 'password') ? 'text' : 'password';
        icon.className = (input.type === 'text') ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    function checkStrength(val) {
        const wrap = document.getElementById('strengthWrap');
        const bar  = document.getElementById('strengthBar');
        const txt  = document.getElementById('strengthText');
        if (!val) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';
        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { w:'25%', c:'#DC2626', t:'Weak' },
            { w:'50%', c:'#F59E0B', t:'Fair' },
            { w:'75%', c:'#2563EB', t:'Good' },
            { w:'100%',c:'#16A34A', t:'Strong' },
        ];
        const l = levels[Math.max(score-1,0)];
        bar.style.width      = l.w;
        bar.style.background = l.c;
        txt.style.color      = l.c;
        txt.textContent      = l.t + ' password';
    }
    </script>

</x-guest-layout>
