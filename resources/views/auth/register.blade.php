<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - PKL SMK Fatahillah</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-yaspat.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-yaspat.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(135deg, rgba(255,245,240,.86) 0%, rgba(255,232,214,.82) 100%),
                url('{{ asset('images/foto smk.jpg') }}') center top/cover no-repeat fixed;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255,140,66,.18) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,107,53,.14) 0%, transparent 50%),
                linear-gradient(180deg, rgba(15,23,42,.18) 0%, rgba(15,23,42,.08) 100%);
            z-index: 0;
        }
        .register-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }
        .register-card {
            border: 1px solid rgba(255,140,66,.15);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(255,140,66,.18);
            overflow: hidden;
            max-width: 960px;
            width: min(960px, 100%);
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(20px);
            animation: cardIn .6s cubic-bezier(.4,0,.2,1);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .register-left {
            background:
                linear-gradient(160deg, rgba(255,140,66,.9) 0%, rgba(255,107,53,.88) 50%, rgba(255,87,34,.9) 100%),
                url('{{ asset('images/foto smk.jpg') }}') center/cover no-repeat;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .register-left::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
            top: -40px;
            right: -40px;
        }
        .register-left::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            bottom: -20px;
            left: -20px;
        }
        .register-left .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.2);
            padding: 8px;
            overflow: hidden;
        }
        .register-left .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .register-right {
            background: #fff;
            padding: 3rem 2.5rem;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: rgba(255,140,66,.1);
            color: #c2410c;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .headline {
            font-size: 1.8rem;
            line-height: 1.15;
            letter-spacing: -0.04em;
            color: #1f2937;
        }
        .form-control {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 16px;
            font-size: .9rem;
            transition: all .25s cubic-bezier(.4,0,.2,1);
        }
        .form-control:focus {
            border-color: #FF8C42;
            box-shadow: 0 0 0 4px rgba(255,140,66,.1);
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-label { font-weight: 600; font-size: .85rem; color: #334155; margin-bottom: 6px; }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .form-control { padding-left: 44px; }
        .input-icon-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color .2s;
            pointer-events: none;
        }
        .input-icon-wrap .form-control:focus ~ .input-icon,
        .input-icon-wrap .form-control:not(:placeholder-shown) ~ .input-icon { color: #FF8C42; }
        .btn-register {
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 50%, #FF5722 100%);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: .95rem;
            color: #fff;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 15px rgba(255,107,53,.3);
            letter-spacing: .3px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,107,53,.4);
            color: #fff;
        }
        .btn-register:active { transform: translateY(0); }
        .alert-register {
            border: none;
            border-radius: 12px;
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            font-size: .875rem;
            font-weight: 500;
            padding: 12px 16px;
        }
        .floating-shapes {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .floating-shapes span {
            position: absolute;
            border-radius: 50%;
            opacity: .06;
            background: #fff;
            animation: float 20s infinite ease-in-out;
        }
        .floating-shapes span:nth-child(1) { width: 200px; height: 200px; top: 10%; left: 5%; animation-delay: 0s; }
        .floating-shapes span:nth-child(2) { width: 120px; height: 120px; top: 60%; right: 10%; animation-delay: -5s; }
        .floating-shapes span:nth-child(3) { width: 80px; height: 80px; bottom: 15%; left: 30%; animation-delay: -10s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        @media (max-width: 767px) {
            body {
                background-attachment: scroll;
            }
            .register-wrapper {
                padding: 1rem;
            }
            .register-right { padding: 2rem 1.5rem; }
            .register-left { padding: 2.25rem 1.5rem; }
            .register-card { width: 100%; }
            .headline { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="floating-shapes"><span></span><span></span><span></span></div>

<div class="register-wrapper">
    <div class="card register-card">
        <div class="row g-0">
            <div class="col-md-5 d-none d-md-flex register-left text-white">
                <div class="position-relative" style="z-index:1;">
                    <div class="eyebrow mb-3">
                        <i class="bi bi-building"></i>
                        PKL SMK Fatahillah
                    </div>
                    <div class="brand-logo mx-auto">
                        <img src="{{ asset('images/logo-yaspat.jpg') }}" alt="SMK Fatahillah">
                    </div>
                    <h3 class="fw-bold mb-2">SMK Fatahillah</h3>
                    <p class="opacity-75 mb-0" style="font-size:.95rem;">Sistem Informasi<br>Praktek Kerja Lapangan</p>
                    <hr class="opacity-25 my-4 mx-auto" style="max-width:80px;">
                    <p class="small opacity-60 mb-0 px-2" style="line-height:1.6;">Buat akun untuk akses PKL, absensi, jurnal, dan dokumentasi dalam satu tempat.</p>
                </div>
            </div>
            <div class="col-md-7 register-right d-flex flex-column justify-content-center">
                <div class="eyebrow mb-3 d-inline-flex d-md-none">
                    <i class="bi bi-building"></i>
                    PKL SMK Fatahillah
                </div>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('login') }}" class="btn-close" style="text-decoration:none;color:#FF8C42;font-size:1.2rem;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold headline mb-0">Buat Akun Baru</h4>
                </div>
                <p class="text-muted mb-4" style="font-size:.9rem;">Daftar untuk mengakses sistem PKL.</p>

                @if($errors->any())
                    <div class="alert-register d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('register.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-icon-wrap">
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap Anda" value="{{ old('name') }}" required>
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-icon-wrap">
                            <input type="email" name="email" class="form-control" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                            <i class="bi bi-lock input-icon"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-icon-wrap">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            <i class="bi bi-lock input-icon"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-register w-100">
                        <i class="bi bi-person-plus me-1"></i> Daftar
                    </button>
                </form>

                <p class="text-center mt-4" style="font-size:.85rem;">
                    Sudah punya akun? <a href="{{ route('login') }}" style="color:#FF8C42;text-decoration:none;font-weight:600;">Masuk sekarang</a>
                </p>

                <p class="text-center text-muted mb-0" style="font-size:.8rem;">&copy; {{ date('Y') }} PKL SMK Fatahillah</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
