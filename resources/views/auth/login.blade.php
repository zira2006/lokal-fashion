<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Lokal Fashion</title>
    <meta name="description" content="Satu halaman login untuk pembeli dan admin Lokal Fashion. Akun penjual masuk dan mendaftar melalui halaman admin.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">

    @php
        $roleContent = [
            'pembeli' => [
                'label' => 'Akun Pembeli',
                'asideLabel' => 'Akun Pembeli',
                'title' => 'Masuk Akun Pembeli | Lokal Fashion',
                'heading' => 'Masuk ke akunmu',
                'subtitle' => 'Masuk dengan akun pembeli atau penjual yang sudah terdaftar untuk melanjutkan belanja.',
                'asideTitle' => 'Masuk dan temukan karya lokal terbaik.',
                'asideDesc' => 'Batik, tenun, hijab, hingga aksesoris handmade dari UMKM Indonesia — semua dalam satu akun yang mudah digunakan.',
                'benefits' => [
                    'Belanja produk asli dan berkualitas dari penjual terverifikasi',
                    'Pantau status pesanan dan riwayat transaksi kapan saja',
                    'Simpan alamat pengiriman agar checkout lebih cepat',
                ],
            ],
            'admin' => [
                'label' => 'Akun Admin',
                'asideLabel' => 'Panel Admin',
                'title' => 'Masuk Akun Admin | Lokal Fashion',
                'heading' => 'Masuk sebagai admin',
                'subtitle' => 'Akun admin dan penjual dapat masuk di sini; pendaftaran penjual juga tersedia.',
                'asideTitle' => 'Satu akses untuk mengelola Lokal Fashion.',
                'asideDesc' => 'Penjual mengelola produk dan pesanan toko; admin mengelola pengguna, toko, dan seluruh transaksi.',
                'benefits' => [
                    'Kelola produk dan pesanan toko',
                    'Pantau pengguna dan toko',
                    'Awasi seluruh transaksi',
                ],
            ],
        ];

        $initialRole = old('role', request('role', 'pembeli'));

        // Akun penjual dan admin sama-sama masuk melalui tab Admin.
        if ($initialRole === 'penjual') {
            $initialRole = 'admin';
        }

        if (! array_key_exists($initialRole, $roleContent)) {
            $initialRole = 'pembeli';
        }
        $content = $roleContent[$initialRole];

        // Tab Admin dipakai untuk login admin/penjual dan pendaftaran penjual.
        $registerRole = old('role', $initialRole === 'admin' ? 'penjual' : 'pembeli');

        if (! in_array($registerRole, ['pembeli', 'penjual'], true)) {
            $registerRole = $initialRole === 'admin' ? 'penjual' : 'pembeli';
        }

        $startOnRegister = $errors->has('name')
            || $errors->has('phone')
            || $errors->has('password_confirmation')
            || old('name')
            || old('phone');
    @endphp

    <style>
        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
        }

        /* ---------- SISI KIRI ---------- */
        .auth-aside {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 46px;
            padding: 52px clamp(32px, 6vw, 88px);
            background: linear-gradient(150deg, #3d2c21 0%, #594637 55%, #7d5c42 100%);
            color: #f7ece0;
        }

        .auth-aside::before,
        .auth-aside::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            pointer-events: none;
        }

        .auth-aside::before {
            width: 460px;
            height: 460px;
            right: -190px;
            top: -150px;
            background: radial-gradient(circle at 30% 30%, rgba(233, 220, 203, .22), transparent 68%);
        }

        .auth-aside::after {
            width: 380px;
            height: 380px;
            left: -170px;
            bottom: -150px;
            background: radial-gradient(circle at 60% 40%, rgba(201, 168, 135, .20), transparent 70%);
        }

        .auth-brand {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fdf6ee;
        }

        .auth-brand small {
            display: block;
            margin-top: 5px;
            font-family: 'DM Sans', sans-serif;
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #cbb6a2;
        }

        .aside-copy {
            max-width: 470px;
        }

        .aside-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 30px;
            background: rgba(255, 255, 255, .07);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: #ecdbc9;
        }

        .aside-copy h1 {
            margin: 0 0 18px;
            font-family: 'Playfair Display', serif;
            font-size: clamp(34px, 3.7vw, 50px);
            font-weight: 600;
            line-height: 1.16;
            color: #fdf6ee;
        }

        .aside-copy > p {
            margin: 0;
            max-width: 420px;
            font-size: 15px;
            line-height: 1.75;
            color: #d8c6b3;
        }

        .auth-benefits {
            display: grid;
            gap: 14px;
            margin: 34px 0 0;
            padding: 0;
            list-style: none;
        }

        .auth-benefits li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            line-height: 1.55;
            color: #e6d6c5;
        }

        .auth-benefits .check {
            flex: 0 0 auto;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
            border-radius: 50%;
            background: rgba(233, 220, 203, .16);
            color: #f3e4d3;
        }

        .aside-quote {
            max-width: 430px;
            padding: 20px 22px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 14px;
            background: rgba(255, 255, 255, .06);
        }

        .aside-quote p {
            margin: 0 0 12px;
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-style: italic;
            line-height: 1.6;
            color: #f2e6d8;
        }

        .aside-quote span {
            font-size: 11px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #c5ad96;
        }

        /* ---------- SISI KANAN ---------- */
        .auth-main {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px clamp(20px, 5vw, 76px);
            background:
                radial-gradient(900px 480px at 110% -10%, #f0e6d8 0%, transparent 60%),
                var(--cream);
        }

        .auth-panel {
            width: 100%;
            max-width: 460px;
        }

        .mobile-brand {
            display: none;
            margin-bottom: 26px;
        }

        .mobile-brand .auth-brand {
            color: var(--dark);
        }

        .mobile-brand .auth-brand small {
            color: var(--muted);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 18px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            transition: color .2s;
        }

        .back-link:hover {
            color: var(--dark);
        }

        .auth-card {
            padding: 34px;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: var(--white);
            box-shadow: 0 22px 55px rgba(89, 70, 55, .10);
        }

        .auth-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            padding: 6px 13px;
            border-radius: 30px;
            background: var(--light-brown);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #8a6649;
        }

        .auth-card h2 {
            margin: 0 0 8px;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
            line-height: 1.25;
            color: var(--dark);
        }

        .auth-subtitle {
            margin: 0 0 22px;
            font-size: 13px;
            line-height: 1.65;
            color: var(--muted);
        }

        /* Tab */
        .auth-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 22px;
            padding: 5px;
            border-radius: 30px;
            background: var(--beige);
        }

        .auth-tab {
            flex: 1;
            padding: 10px 18px;
            border: none;
            border-radius: 25px;
            background: transparent;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            color: #8b7563;
            cursor: pointer;
            transition: all .22s;
        }

        .auth-tab:hover {
            color: var(--dark);
        }

        .auth-tab.active {
            background: var(--dark);
            color: #fff;
            box-shadow: 0 6px 16px rgba(89, 70, 55, .20);
        }

        /* Pemilih role */
        .role-switch {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 18px;
        }

        .role-choice {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 11px 6px 9px;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            background: #fff;
            font-family: inherit;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
        }

        .role-choice svg {
            width: 17px;
            height: 17px;
            color: var(--brown);
            transition: color .2s;
        }

        .role-choice:hover {
            border-color: var(--soft-brown);
            color: var(--dark);
        }

        .role-choice.active {
            border-color: var(--brown);
            background: #fdf6ee;
            color: var(--dark);
            box-shadow: inset 0 0 0 1px rgba(177, 138, 106, .25);
        }

        .role-choice.active svg {
            color: var(--dark);
        }

        /* Form */
        .form-group {
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--dark);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: #fff;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-wrap input::placeholder {
            color: #b7a99d;
        }

        .input-wrap input:focus {
            border-color: var(--brown);
            box-shadow: 0 0 0 3px rgba(177, 138, 106, .14);
        }

        .input-wrap.has-toggle input {
            padding-right: 78px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            padding: 6px 9px;
            border: none;
            border-radius: 8px;
            background: transparent;
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            color: var(--brown);
            cursor: pointer;
            transition: background .2s;
        }

        .toggle-password:hover {
            background: var(--light-brown);
        }

        .form-error {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.45;
            color: #a8442e;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 4px 0 22px;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 12.5px;
            color: var(--text);
            cursor: pointer;
            user-select: none;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--dark);
            cursor: pointer;
        }

        .secure-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--muted);
        }

        .btn {
            display: inline-block;
            padding: 13px 24px;
            border: none;
            border-radius: 25px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-primary {
            background: var(--dark);
            color: #fff;
        }

        .btn-primary:hover {
            background: #6d5340;
            box-shadow: 0 8px 20px rgba(89, 70, 55, .20);
        }

        .btn-block {
            width: 100%;
        }

        .btn-submit {
            padding: 14px 24px;
            font-size: 14px;
            letter-spacing: .2px;
        }

        .alert-error {
            margin-bottom: 18px;
            padding: 12px 15px;
            border: 1px solid #eec5bc;
            border-radius: 10px;
            background: #fbe0dc;
            font-size: 12.5px;
            line-height: 1.5;
            color: #a8442e;
        }

        /* Info admin */
        .admin-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 22px;
            padding: 12px 14px;
            border: 1px solid #dccdbd;
            border-radius: 10px;
            background: #fbf6ef;
            font-size: 11.5px;
            line-height: 1.55;
            color: var(--muted);
        }

        .admin-note svg {
            flex: 0 0 auto;
            margin-top: 1px;
            color: #8a6649;
        }

        .auth-footer-text {
            margin-top: 18px;
            font-size: 12.5px;
            text-align: center;
            color: var(--muted);
        }

        .auth-footer-text a {
            font-weight: 700;
            color: #8a6649;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
        }

        /* Daftar */
        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .role-options.is-seller-only {
            grid-template-columns: 1fr;
        }

        .role-option {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 12px;
            border: 1.5px solid var(--line);
            border-radius: 11px;
            background: #fff;
            font-size: 12.5px;
            cursor: pointer;
            transition: all .2s;
        }

        .role-option input {
            margin: 2px 0 0;
            accent-color: var(--dark);
            cursor: pointer;
        }

        .role-option strong {
            display: block;
            font-size: 12.5px;
            color: var(--dark);
        }

        .role-option small {
            display: block;
            margin-top: 1px;
            font-size: 10.5px;
            color: var(--muted);
        }

        .role-option.selected {
            border-color: var(--brown);
            background: #fdf6ee;
        }

        .is-hidden {
            display: none !important;
        }

        @media (max-width: 960px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-aside {
                display: none;
            }

            .auth-main {
                min-height: 100vh;
                padding: 34px 6%;
            }

            .mobile-brand {
                display: block;
            }
        }

        @media (max-width: 520px) {
            .auth-main {
                padding: 24px 16px;
            }

            .auth-card {
                padding: 26px 20px;
                border-radius: 18px;
            }

            .auth-card h2 {
                font-size: 24px;
            }

            .role-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        {{-- Sisi kiri: cerita brand --}}
        <aside class="auth-aside">
            <a href="{{ route('home') }}" class="auth-brand">
                Lokal Fashion
                <small>Gaya Lokal, Cerita Istimewa</small>
            </a>

            <div class="aside-copy">
                <span class="aside-eyebrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3l2.5 5.5L20 10l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-1.5z"></path>
                    </svg>
                    <span id="asideEyebrowText">{{ $content['asideLabel'] }}</span>
                </span>

                <h1 id="asideTitle">{{ $content['asideTitle'] }}</h1>
                <p id="asideDesc">{{ $content['asideDesc'] }}</p>

                <ul class="auth-benefits">
                    <li>
                        <span class="check">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                        </span>
                        <span id="benefit1">{{ $content['benefits'][0] }}</span>
                    </li>
                    <li>
                        <span class="check">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                        </span>
                        <span id="benefit2">{{ $content['benefits'][1] }}</span>
                    </li>
                    <li>
                        <span class="check">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"></path></svg>
                        </span>
                        <span id="benefit3">{{ $content['benefits'][2] }}</span>
                    </li>
                </ul>
            </div>

            <div class="aside-quote">
                <p>&ldquo;Setiap jahitan punya cerita. Setiap pembelian membantu pengrajin lokal terus berkarya.&rdquo;</p>
                <span>Lokal Fashion &middot; Banda Aceh</span>
            </div>
        </aside>

        {{-- Sisi kanan: form --}}
        <main class="auth-main">
            <div class="auth-panel">
                <div class="mobile-brand">
                    <a href="{{ route('home') }}" class="auth-brand">
                        Lokal Fashion
                        <small>Gaya Lokal, Cerita Istimewa</small>
                    </a>
                </div>

                <a href="{{ route('home') }}" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
                    Kembali ke Beranda
                </a>

                <div class="auth-card">
                    <span class="auth-eyebrow">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span id="cardEyebrowText">{{ $content['label'] }}</span>
                    </span>

                    <div class="auth-tabs" id="authTabs" role="tablist" aria-label="Pilih masuk atau daftar">
                        <button type="button" class="auth-tab {{ $startOnRegister ? '' : 'active' }}" id="tabMasuk"
                                role="tab" aria-selected="{{ $startOnRegister ? 'false' : 'true' }}" aria-controls="panel-masuk"
                                onclick="showTab('masuk')">Masuk</button>
                        <button type="button" class="auth-tab {{ $startOnRegister ? 'active' : '' }}" id="tabDaftar"
                                role="tab" aria-selected="{{ $startOnRegister ? 'true' : 'false' }}" aria-controls="panel-daftar"
                                onclick="showTab('daftar')">Daftar</button>
                    </div>

                    {{-- ================= FORM MASUK ================= --}}
                    <section id="panel-masuk" role="tabpanel" aria-labelledby="tabMasuk" class="{{ $startOnRegister ? 'is-hidden' : '' }}">
                        <h2 id="loginHeading">{{ $content['heading'] }}</h2>
                        <p class="auth-subtitle" id="loginSubtitle">{{ $content['subtitle'] }}</p>

                        @if ($errors->has('email') && ! $startOnRegister)
                            <div class="alert-error" role="alert">
                                Email atau password belum sesuai. Silakan periksa kembali data kamu.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" novalidate>
                            @csrf
                            <input type="hidden" name="role" id="login-role" value="{{ $initialRole }}">

                            <div class="role-switch" role="group" aria-label="Pilih tipe akun">
                                <button type="button" class="role-choice {{ $initialRole === 'pembeli' ? 'active' : '' }}" data-role="pembeli"
                                        aria-pressed="{{ $initialRole === 'pembeli' ? 'true' : 'false' }}" onclick="selectRole('pembeli')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <path d="M3 6h18"></path>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                    <span>Pembeli</span>
                                </button>
                                <button type="button" class="role-choice {{ $initialRole === 'admin' ? 'active' : '' }}" data-role="admin"
                                        aria-pressed="{{ $initialRole === 'admin' ? 'true' : 'false' }}" onclick="selectRole('admin')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        <path d="M9 12l2 2 4-4"></path>
                                    </svg>
                                    <span>Admin</span>
                                </button>
                            </div>
                            @error('role')
                                <div class="form-error" style="margin: -8px 0 14px;">{{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-wrap">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                           placeholder="nama@email.com" autocomplete="email" required autofocus>
                                </div>
                                @error('email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-wrap has-toggle">
                                    <input type="password" id="password" name="password" placeholder="Masukkan password"
                                           autocomplete="current-password" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Tampilkan password">Lihat</button>
                                </div>
                                @error('password')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <label class="checkbox">
                                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                    Ingat saya
                                </label>
                                <span class="secure-note">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    Data kamu aman
                                </span>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-submit">Masuk</button>
                        </form>

                        <div class="admin-note {{ $initialRole === 'admin' ? '' : 'is-hidden' }}" id="adminNote">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 16v-4M12 8h.01"></path>
                            </svg>
                            <span>Akun penjual dan admin masuk melalui halaman ini. Akun admin tidak dapat didaftarkan sendiri; untuk mendaftar sebagai penjual, gunakan tab Daftar.</span>
                        </div>

                        <p class="auth-footer-text" id="registerPrompt">
                            <span id="registerPromptText">{{ $initialRole === 'admin' ? 'Belum punya akun penjual?' : 'Belum punya akun?' }}</span>
                            <a href="#daftar" onclick="showTab('daftar'); return false;">Daftar sekarang</a>
                        </p>
                    </section>

                    {{-- ================= FORM DAFTAR ================= --}}
                    <section id="panel-daftar" role="tabpanel" aria-labelledby="tabDaftar" class="{{ $startOnRegister ? '' : 'is-hidden' }}">
                        <h2 id="registerHeading">{{ $initialRole === 'admin' ? 'Daftar sebagai penjual' : 'Buat akun baru' }}</h2>
                        <p class="auth-subtitle" id="registerSubtitle">
                            {{ $initialRole === 'admin' ? 'Buat akun penjual untuk membuka toko lokalmu di Lokal Fashion.' : 'Daftar gratis sebagai pembeli, atau buka toko kamu sebagai penjual.' }}
                        </p>

                        <form method="POST" action="{{ route('register') }}" id="registerForm">
                            @csrf

                            <div class="form-group">
                                <label for="name">Nama lengkap</label>
                                <div class="input-wrap">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                           placeholder="Contoh: Siti Aminah" autocomplete="name" required>
                                </div>
                                @error('name')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="reg-email">Email</label>
                                <div class="input-wrap">
                                    <input type="email" id="reg-email" name="email" value="{{ old('email') }}"
                                           placeholder="nama@email.com" autocomplete="email" required>
                                </div>
                                @error('email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">No. HP</label>
                                <div class="input-wrap">
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="08xxxxxxxxxx" autocomplete="tel" required>
                                </div>
                                @error('phone')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="reg-password">Password</label>
                                <div class="input-wrap has-toggle">
                                    <input type="password" id="reg-password" name="password" placeholder="Minimal 6 karakter"
                                           autocomplete="new-password" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('reg-password', this)" aria-label="Tampilkan password">Lihat</button>
                                </div>
                                @error('password')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi password</label>
                                <div class="input-wrap">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           placeholder="Ulangi password" autocomplete="new-password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label id="registerRoleLabel">{{ $initialRole === 'admin' ? 'Tipe akun' : 'Daftar sebagai' }}</label>
                                <div class="role-options {{ $initialRole === 'admin' ? 'is-seller-only' : '' }}" id="registerRoleOptions">
                                    <label class="role-option {{ $initialRole === 'admin' ? 'is-hidden' : '' }} {{ $registerRole === 'pembeli' ? 'selected' : '' }}" data-register-role="pembeli">
                                        <input type="radio" name="role" value="pembeli" {{ $registerRole === 'pembeli' ? 'checked' : '' }}>
                                        <span>
                                            <strong>Pembeli</strong>
                                            <small>Belanja produk lokal</small>
                                        </span>
                                    </label>
                                    <label class="role-option {{ $registerRole === 'penjual' ? 'selected' : '' }}" data-register-role="penjual">
                                        <input type="radio" name="role" value="penjual" {{ $registerRole === 'penjual' ? 'checked' : '' }}>
                                        <span>
                                            <strong>Penjual</strong>
                                            <small>Buka toko sendiri</small>
                                        </span>
                                    </label>
                                </div>
                                @error('role')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-submit" style="margin-top: 4px;">Daftar</button>
                        </form>

                        <p class="auth-footer-text">
                            Sudah punya akun? <a href="#masuk" onclick="showTab('masuk'); return false;">Masuk di sini</a>
                        </p>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
        const panelMasuk = document.getElementById('panel-masuk');
        const panelDaftar = document.getElementById('panel-daftar');
        const tabMasuk = document.getElementById('tabMasuk');
        const tabDaftar = document.getElementById('tabDaftar');

        const roleContent = {
            pembeli: {
                label: 'Akun Pembeli',
                asideLabel: 'Akun Pembeli',
                title: 'Masuk Akun Pembeli | Lokal Fashion',
                heading: 'Masuk ke akunmu',
                subtitle: 'Selamat datang kembali. Masukkan email dan password untuk melanjutkan belanja.',
                asideTitle: 'Masuk dan temukan karya lokal terbaik.',
                asideDesc: 'Batik, tenun, hijab, hingga aksesoris handmade dari UMKM Indonesia — semua dalam satu akun yang mudah digunakan.',
                benefits: [
                    'Belanja produk asli dan berkualitas dari penjual terverifikasi',
                    'Pantau status pesanan dan riwayat transaksi kapan saja',
                    'Simpan alamat pengiriman agar checkout lebih cepat'
                ]
            },
            admin: {
                label: 'Akun Admin',
                asideLabel: 'Panel Admin',
                title: 'Masuk Akun Admin | Lokal Fashion',
                heading: 'Masuk sebagai admin',
                subtitle: 'Akun admin dan penjual dapat masuk di sini; pendaftaran penjual juga tersedia.',
                asideTitle: 'Satu akses untuk mengelola Lokal Fashion.',
                asideDesc: 'Penjual mengelola produk dan pesanan toko; admin mengelola pengguna, toko, dan seluruh transaksi.',
                benefits: [
                    'Kelola produk dan pesanan toko',
                    'Pantau pengguna dan toko',
                    'Awasi seluruh transaksi'
                ]
            }
        };

        let currentRole = @json($initialRole);

        function selectRole(role) {
            if (!roleContent[role]) {
                role = 'pembeli';
            }

            currentRole = role;
            const data = roleContent[role];

            document.getElementById('login-role').value = role;
            document.getElementById('cardEyebrowText').textContent = data.label;
            document.getElementById('asideEyebrowText').textContent = data.asideLabel;
            document.getElementById('loginHeading').textContent = data.heading;
            document.getElementById('loginSubtitle').textContent = data.subtitle;
            document.getElementById('asideTitle').textContent = data.asideTitle;
            document.getElementById('asideDesc').textContent = data.asideDesc;
            document.getElementById('benefit1').textContent = data.benefits[0];
            document.getElementById('benefit2').textContent = data.benefits[1];
            document.getElementById('benefit3').textContent = data.benefits[2];
            document.title = data.title;

            document.querySelectorAll('.role-choice').forEach(function (button) {
                const active = button.dataset.role === role;
                button.classList.toggle('active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            const isAdmin = role === 'admin';

            document.getElementById('adminNote').classList.toggle('is-hidden', !isAdmin);
            updateRegisterRole(isAdmin);
        }

        function updateRegisterRole(isAdmin) {
            const form = document.getElementById('registerForm');
            if (!form) return;

            const options = document.getElementById('registerRoleOptions');
            const buyerOption = form.querySelector('[data-register-role="pembeli"]');
            const sellerOption = form.querySelector('[data-register-role="penjual"]');
            const buyerRadio = form.querySelector('input[name="role"][value="pembeli"]');
            const sellerRadio = form.querySelector('input[name="role"][value="penjual"]');
            const heading = document.getElementById('registerHeading');
            const subtitle = document.getElementById('registerSubtitle');
            const roleLabel = document.getElementById('registerRoleLabel');
            const promptText = document.getElementById('registerPromptText');

            if (isAdmin) {
                buyerOption.classList.add('is-hidden');
                sellerOption.classList.remove('is-hidden');
                options.classList.add('is-seller-only');

                sellerRadio.checked = true;
                sellerOption.classList.add('selected');
                buyerOption.classList.remove('selected');

                heading.textContent = 'Daftar sebagai penjual';
                subtitle.textContent = 'Buat akun penjual untuk membuka toko lokalmu di Lokal Fashion.';
                roleLabel.textContent = 'Tipe akun';
                promptText.textContent = 'Belum punya akun penjual?';
            } else {
                buyerOption.classList.remove('is-hidden');
                sellerOption.classList.remove('is-hidden');
                options.classList.remove('is-seller-only');

                buyerRadio.checked = true;
                buyerOption.classList.add('selected');
                sellerOption.classList.remove('selected');

                heading.textContent = 'Buat akun baru';
                subtitle.textContent = 'Daftar gratis sebagai pembeli, atau buka toko kamu sebagai penjual.';
                roleLabel.textContent = 'Daftar sebagai';
                promptText.textContent = 'Belum punya akun?';
            }
        }

        function showTab(tab) {
            const isMasuk = tab === 'masuk';

            panelMasuk.classList.toggle('is-hidden', !isMasuk);
            panelDaftar.classList.toggle('is-hidden', isMasuk);

            tabMasuk.classList.toggle('active', isMasuk);
            tabDaftar.classList.toggle('active', !isMasuk);
            tabMasuk.setAttribute('aria-selected', isMasuk ? 'true' : 'false');
            tabDaftar.setAttribute('aria-selected', isMasuk ? 'false' : 'true');

            if (window.history.replaceState) {
                window.history.replaceState(null, '', isMasuk ? '#masuk' : '#daftar');
            }

            if (!isMasuk) {
                const nameInput = document.getElementById('name');
                if (nameInput) nameInput.focus({ preventScroll: true });
            }
        }

        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const show = input.type === 'password';

            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Sembunyi' : 'Lihat';
            button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        }

        // Sorot pilihan role pada form daftar
        document.querySelectorAll('.role-option input').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.role-option').forEach(function (el) {
                    el.classList.remove('selected');
                });
                this.closest('.role-option').classList.add('selected');
            });
        });

        // Sinkronkan tampilan dengan role awal (dari URL / old input)
        selectRole(currentRole);

        // Dukung tautan langsung ke tab daftar, termasuk pendaftaran penjual dari tab Admin.
        if (window.location.hash === '#daftar') {
            showTab('daftar');
        }
    </script>
</body>
</html>
