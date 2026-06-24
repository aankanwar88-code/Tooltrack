{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — ToolTrack</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── Sisi Kiri: Background Foto ────────────────── */
        .left-panel {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 48px;

            /* Foto background dari Unsplash — industri/workshop */
            background-image: url(https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=1400&q=80&fit=crop);
            background-size: cover;
            background-position: center;
        }

        /* Overlay gelap + abu-abu di atas foto */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(30, 30, 35, 0.75) 0%,
                rgba(55, 65, 81, 0.60) 50%,
                rgba(17, 24, 39, 0.80) 100%
            );
        }

        /* Noise texture overlay untuk kesan modern */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .left-content { position: relative; z-index: 1; }

        .left-logo {
            position: absolute;
            top: 40px; left: 48px;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .left-logo .icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
        }

        .left-logo .brand { color: #fff; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
        .left-logo .brand span { color: #9CA3AF; font-weight: 400; font-size: 13px; display: block; margin-top: 1px; }

        .left-tagline {
            color: #fff;
        }

        .left-tagline h1 {
            font-size: clamp(28px, 3vw, 42px);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
        }

        .left-tagline h1 span { color: #D1D5DB; }

        .left-tagline p {
            font-size: 15px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            max-width: 420px;
        }

        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 28px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.15);
            color: #E5E7EB;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .pill i { font-size: 11px; color: #9CA3AF; }

        /* ── Sisi Kanan: Form Login ─────────────────────── */
        .right-panel {
            width: 440px;
            min-width: 440px;
            background: #F9FAFB;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 48px;
            position: relative;
            overflow-y: auto;
        }

        /* Subtle strip di kiri panel kanan */
        .right-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #6B7280 0%, #374151 50%, #6B7280 100%);
        }

        .form-header { margin-bottom: 36px; }

        .form-header .welcome {
            font-size: 13px;
            font-weight: 500;
            color: #6B7280;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .form-header p {
            margin-top: 8px;
            font-size: 14px;
            color: #9CA3AF;
        }

        /* Form elements */
        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 14px;
            pointer-events: none;
            transition: color .2s;
        }

        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            background: #fff;
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-wrap input:focus {
            border-color: #6B7280;
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.12);
        }

        .input-wrap input:focus + .input-icon,
        .input-wrap input:focus ~ .input-icon {
            color: #374151;
        }

        .input-wrap .toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9CA3AF; cursor: pointer;
            font-size: 14px; padding: 4px;
            transition: color .2s;
        }

        .input-wrap .toggle-pw:hover { color: #374151; }

        .input-wrap input.is-invalid { border-color: #EF4444; }
        .invalid-feedback { color: #EF4444; font-size: 12px; margin-top: 5px; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #6B7280;
        }

        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #374151;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: #6B7280;
            text-decoration: none;
            transition: color .2s;
        }

        .forgot-link:hover { color: #111827; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #1F2937;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: background .2s, transform .15s, box-shadow .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: #374151;
            box-shadow: 0 4px 12px rgba(31,41,55,0.25);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login .spinner {
            display: none;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .divider {
            text-align: center;
            margin: 28px 0 20px;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0; right: 0;
            top: 50%;
            height: 1px;
            background: #E5E7EB;
        }

        .divider span {
            position: relative;
            background: #F9FAFB;
            padding: 0 12px;
            font-size: 12px;
            color: #9CA3AF;
        }

        /* Role info cards */
        .role-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .role-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .role-card .r-icon {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .role-card .r-name {
            font-size: 11px;
            font-weight: 700;
            color: #374151;
            display: block;
            margin-bottom: 2px;
        }

        .role-card .r-email {
            font-size: 9.5px;
            color: #9CA3AF;
            font-family: monospace;
        }

        /* Alert error */
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #991B1B;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-error i { margin-top: 1px; flex-shrink: 0; }

        /* Footer */
        .form-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 12px;
            color: #9CA3AF;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel {
                width: 100%;
                min-width: unset;
                padding: 40px 28px;
            }
        }
    </style>
</head>
<body>

    <!-- ── Kiri: Foto Background ──────────────────────── -->
    <div class="left-panel">
        <div class="left-logo">
            <div class="icon"><i class="fas fa-wrench"></i></div>
            <div class="brand">
                ToolTrack
                <span>Sistem Manajemen Alat Kerja</span>
            </div>
        </div>

        <div class="left-content left-tagline">
            <h1>Kelola Alat Kerja<br><span>Lebih Mudah & Efisien</span></h1>
            <p>Platform terpadu untuk inventaris, peminjaman, pengembalian, hingga jadwal kalibrasi alat kerja Anda.</p>

            <div class="feature-pills">
                <span class="pill"><i class="fas fa-tools"></i> Inventaris Alat</span>
                <span class="pill"><i class="fas fa-clipboard-list"></i> Peminjaman</span>
                <span class="pill"><i class="fas fa-ruler-combined"></i> Kalibrasi</span>
                <span class="pill"><i class="fas fa-chart-bar"></i> Laporan PDF / Excel</span>
                <span class="pill"><i class="fas fa-users-cog"></i> Multi Role</span>
            </div>
        </div>
    </div>

    <!-- ── Kanan: Form Login ───────────────────────────── -->
    <div class="right-panel">

        <div class="form-header">
            <div class="welcome">Selamat Datang</div>
            <h2>Masuk ke Akun Anda</h2>
            <p>Gunakan email dan password yang terdaftar.</p>
        </div>

        {{-- Session Error --}}
        @if(session('status'))
            <div class="alert-error" style="background:#F0FDF4;border-color:#BBF7D0;color:#166534">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@perusahaan.com"
                        autocomplete="email"
                        autofocus
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required>
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-pw" onclick="togglePw()" tabindex="-1">
                        <i class="fas fa-eye" id="pwEye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="form-options">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login" id="submitBtn">
                <span class="spinner" id="spinner"></span>
                <i class="fas fa-arrow-right-to-bracket" id="submitIcon"></i>
                <span id="submitText">Masuk</span>
            </button>
        </form>

        {{-- Demo accounts --}}
        <div class="divider"><span>Akun Demo</span></div>

        <div class="role-cards">
            <div class="role-card" onclick="fillLogin('admin@tooltrack.id')" style="cursor:pointer" title="Klik untuk isi otomatis">
                <div class="r-icon">🛡️</div>
                <span class="r-name">Admin</span>
                <span class="r-email">admin@tooltrack.id</span>
            </div>
            <div class="role-card" onclick="fillLogin('petugas@tooltrack.id')" style="cursor:pointer" title="Klik untuk isi otomatis">
                <div class="r-icon">👷</div>
                <span class="r-name">Petugas</span>
                <span class="r-email">petugas@tooltrack.id</span>
            </div>
            <div class="role-card" onclick="fillLogin('budi@tooltrack.id')" style="cursor:pointer" title="Klik untuk isi otomatis">
                <div class="r-icon">👤</div>
                <span class="r-name">Peminjam</span>
                <span class="r-email">budi@tooltrack.id</span>
            </div>
        </div>

        <div class="form-footer">
            &copy; {{ date('Y') }} ToolTrack &mdash; Sistem Manajemen Alat Kerja
        </div>

    </div>

    <script>
        // Toggle password visibility
        function togglePw() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('pwEye');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Loading state saat submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('spinner').style.display  = 'block';
            document.getElementById('submitIcon').style.display = 'none';
            document.getElementById('submitText').textContent  = 'Memproses...';
        });

        // Klik kartu demo → isi email & password otomatis
        function fillLogin(email) {
            document.getElementById('email').value    = email;
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
        }
    </script>

</body>
</html>
