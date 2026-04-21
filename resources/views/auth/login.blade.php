<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent:      #2563eb;
            --accent-light:#eff4ff;
            --accent-dark: #1d4ed8;
            --accent-glow: rgba(37, 99, 235, 0.45);
            --ink-900:     #0d1117;
            --ink-700:     #1e2535;
            --ink-500:     #64748b;
            --ink-300:     #cbd5e1;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0a0f1e;
        }

        /* ─── LEFT PANEL ─────────────────────────────────────── */
        .left-panel {
            position: relative;
            width: 55%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 70px;
            overflow: hidden;
            background: linear-gradient(145deg, #0d1b4b 0%, #1a3a8f 45%, #2563eb 100%);
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.55;
            animation: floatBlob 12s ease-in-out infinite;
        }
        .blob-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, #3b82f6 0%, #1d4ed8 100%);
            top: -160px; left: -160px;
            animation-delay: 0s;
        }
        .blob-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, #60a5fa 0%, #2563eb 100%);
            bottom: -100px; right: -80px;
            animation-delay: -4s;
        }
        .blob-3 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, #93c5fd 0%, #3b82f6 100%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -8s;
        }
        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -40px) scale(1.07); }
            66%       { transform: translate(-20px, 25px) scale(0.95); }
        }

        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.65);
            animation: drift linear infinite;
        }
        @keyframes drift {
            0%   { transform: translateY(110vh) translateX(0);    opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.5; }
            100% { transform: translateY(-10vh) translateX(30px); opacity: 0; }
        }

        .panel-content {
            position: relative;
            z-index: 2;
            max-width: 520px;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 7px 16px;
            border-radius: 100px;
            margin-bottom: 32px;
            animation: fadeInUp 0.6s ease both;
        }
        .badge-pill .dot {
            width: 7px; height: 7px;
            background: #4ade80;
            border-radius: 50%;
            animation: blink 1.6s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        .panel-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 3.2vw, 3.2rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            margin-bottom: 18px;
            animation: fadeInUp 0.7s 0.1s ease both;
        }
        .panel-title span {
            background: linear-gradient(90deg, #93c5fd, #bfdbfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .panel-sub {
            font-size: 0.97rem;
            color: rgba(255,255,255,0.72);
            line-height: 1.7;
            margin-bottom: 40px;
            animation: fadeInUp 0.7s 0.2s ease both;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            animation: fadeInUp 0.7s 0.3s ease both;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.85);
            font-size: 0.92rem;
            font-weight: 500;
        }
        .feature-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1rem;
            color: #93c5fd;
            transition: background 0.3s;
        }
        .feature-item:hover .feature-icon {
            background: rgba(255,255,255,0.22);
        }

        /* ─── RIGHT PANEL ────────────────────────────────────── */
        .right-panel {
            width: 45%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px 40px;
            background: #f8faff;
            position: relative;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(37,99,235,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .form-wrapper {
            width: 100%;
            max-width: 390px;
            animation: fadeInRight 0.8s 0.1s ease both;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(24px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-mark {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 6px 18px var(--accent-glow);
        }

        .form-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--ink-900);
            margin-bottom: 3px;
        }
        .form-subheading {
            font-size: 0.85rem;
            color: var(--ink-500);
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--ink-700);
            letter-spacing: 0.4px;
            margin-bottom: 5px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 14px;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ink-500);
            font-size: 0.88rem;
            pointer-events: none;
            transition: color 0.3s;
            z-index: 1;
        }

        .form-control-custom {
            width: 100%;
            padding: 11px 40px 11px 38px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.88rem;
            color: var(--ink-900);
            background: #ffffff;
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
        }
        .form-control-custom::placeholder { color: #b0bec5; }
        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37,99,235,0.1);
        }
        .form-control-custom.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.1);
        }
        .input-group-custom:focus-within .input-icon {
            color: var(--accent);
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--ink-500);
            font-size: 1rem;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.3s;
            z-index: 1;
        }
        .toggle-pw:hover { color: var(--accent); }

        .invalid-feedback-custom {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: -10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .form-check-label {
            font-size: 0.8rem;
            color: var(--ink-500);
        }

        .forgot-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            transition: color 0.3s;
        }
        .forgot-link:hover { color: var(--accent-dark); text-decoration: underline; }

        .btn-signin {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 4px;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }
        .btn-signin::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px var(--accent-glow);
        }
        .btn-signin:hover::after { opacity: 1; }
        .btn-signin:active { transform: translateY(0); }

        /* demo credentials */
        .demo-box {
            margin-top: 16px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f0fe 100%);
            border: 1px solid rgba(37,99,235,0.15);
            border-radius: 12px;
            padding: 13px 16px;
        }
        .demo-box-header {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 14px;
        }
        .cred-row {
            font-size: 0.7rem;
            color: var(--ink-500);
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .cred-row strong {
            color: var(--ink-700);
            font-size: 0.69rem;
        }
        .cred-password {
            margin-top: 10px;
            padding-top: 9px;
            border-top: 1px dashed rgba(37,99,235,0.2);
            text-align: center;
            font-size: 0.73rem;
            color: var(--accent-dark);
            font-weight: 600;
        }

        /* alert */
        .alert-custom {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.82rem;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .footer-note {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 14px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ─── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; overflow-x: hidden; }
            .left-panel { display: none; }
            .right-panel {
                width: 100%;
                height: auto;
                min-height: 100vh;
                overflow: visible;
                padding: 40px 24px;
                background: linear-gradient(160deg, #0d1b4b 0%, #1a3a8f 40%, #2563eb 100%);
            }
            .right-panel::before { display: none; }
            .form-wrapper {
                background: rgba(255,255,255,0.97);
                border-radius: 20px;
                padding: 30px 24px;
                max-width: 440px;
                box-shadow: 0 24px 64px rgba(0,0,0,0.35);
            }
            .footer-note { color: rgba(255,255,255,0.5); }
        }

        @media (max-width: 480px) {
            .right-panel { padding: 24px 16px; }
            .form-wrapper { padding: 24px 18px; }
            .cred-grid { grid-template-columns: 1fr; gap: 4px; }
        }
    </style>
</head>
<body>

    <!-- ── LEFT PANEL ── -->
    <div class="left-panel">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div id="particles"></div>

        <div class="panel-content">
            <div class="badge-pill">
                <span class="dot"></span>
                School Management Platform
            </div>

            <h1 class="panel-title">
                Powering <span>smarter</span><br>schools, every day.
            </h1>

            <p class="panel-sub">
                A unified ERP solution for administrators, teachers, students,
                and parents — everything your institution needs in one place.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                    Role-based access for all staff &amp; students
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
                    Real-time analytics &amp; performance tracking
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    Enterprise-grade security &amp; data privacy
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                    Attendance, timetables &amp; exam management
                </div>
            </div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="right-panel">
        <div class="form-wrapper">

            <div class="logo-mark">
                <i class="bi bi-mortarboard-fill text-white fs-4"></i>
            </div>

            <h2 class="form-heading">Welcome back</h2>
            <p class="form-subheading">Sign in to your School ERP account</p>

            @if(session('error'))
            <div class="alert-custom">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                        <input type="email"
                               class="form-control-custom @error('email') is-invalid @enderror"
                               id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@school.com"
                               required autocomplete="email">
                    </div>
                    @error('email')
                        <div class="invalid-feedback-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="bi bi-lock"></i></span>
                        <input type="password"
                               class="form-control-custom @error('password') is-invalid @enderror"
                               id="password" name="password"
                               placeholder="Enter your password"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember / Forgot -->
                <div class="d-flex justify-content-between align-items-center mb-2 mt-0">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-signin">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="demo-box">
                <div class="demo-box-header">
                    <i class="bi bi-info-circle-fill"></i> Demo Login Credentials
                </div>
                <div class="cred-grid">
                    <div class="cred-row"><strong>Admin</strong>admin@schoolerp.com</div>
                    <div class="cred-row"><strong>Principal</strong>principal@schoolerp.com</div>
                    <div class="cred-row"><strong>Teacher</strong>teacher@schoolerp.com</div>
                    <div class="cred-row"><strong>Accountant</strong>accountant@schoolerp.com</div>
                    <div class="cred-row"><strong>Librarian</strong>librarian@schoolerp.com</div>
                    {{-- <div class="cred-row"><strong>Student</strong>student@schoolerp.com</div> --}}
                </div>
                <div class="cred-password">
                    <i class="bi bi-key-fill me-1"></i> Password for all: <code>password</code>
                </div>
            </div>

        </div><!-- /form-wrapper -->

        <p class="footer-note">© 2025 School ERP System. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* toggle password visibility */
        function togglePassword() {
            var inp  = document.getElementById('password');
            var icon = document.getElementById('toggleIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        /* floating particles on left panel */
        (function spawnParticles() {
            var container = document.getElementById('particles');
            if (!container) return;
            for (var i = 0; i < 18; i++) {
                var p    = document.createElement('div');
                var size = Math.random() * 4 + 2;
                p.className = 'particle';
                p.style.width             = size + 'px';
                p.style.height            = size + 'px';
                p.style.left              = (Math.random() * 100) + '%';
                p.style.bottom            = '-10px';
                p.style.opacity           = (Math.random() * 0.5 + 0.2).toString();
                p.style.animationDuration = (Math.random() * 14 + 10) + 's';
                p.style.animationDelay    = (Math.random() * -20) + 's';
                container.appendChild(p);
            }
        })();
    </script>
</body>
</html>