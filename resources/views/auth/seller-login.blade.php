
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Penjual | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        .seller-login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .seller-login-hero {
            background: linear-gradient(135deg, #3d2b1f 0%, #5a4030 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 12%;
            color: #f5ebe0;
        }

        .seller-login-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(30px, 4vw, 44px);
            margin: 0 0 16px;
            line-height: 1.2;
        }

        .seller-login-hero p {
            color: #c9b6a3;
            font-size: 14px;
            line-height: 1.7;
        }

        .seller-brand {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 50px;
            letter-spacing: 1px;
        }

        .seller-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 25px;
            padding: 8px 18px;
            font-size: 12px;
            margin-bottom: 25px;
            color: #e8d5c0;
        }

        .seller-form-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 10%;
            background: var(--cream);
        }

        .seller-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 40px;
        }

        .seller-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--dark);
            margin: 0 0 8px;
        }

        .seller-card .subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: var(--brown);
        }

        .form-error {
            color: #a8442e;
            font-size: 12px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--dark);
            color: #fff;
        }

        .btn-primary:hover {
            background: #6d5340;
        }

        .btn-block {
            width: 100%;
        }

        .seller-footer-text {
            text-align: center;
            font-size: 13px;
            color: var(--muted);
            margin-top: 22px;
        }

        .seller-footer-text a {
            color: var(--brown);
            font-weight: 600;
            text-decoration: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 25px;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--dark);
        }

        .seller-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 30px;
        }

        .seller-feature {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            color: #c9b6a3;
        }

        .seller-feature strong {
            display: block;
            color: #f5ebe0;
            font-size: 13px;
            margin-bottom: 3px;
        }

        @media (max-width: 800px) {
            .seller-login-page {
                grid-template-columns: 1fr;
            }
            .seller-login-hero {
                display: none;
            }
            .seller-form-side {
                padding: 40px 6%;
            }
            .seller-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="seller-login-page">
        <div class="seller-login-hero">
            <div class="seller-brand">Lokal Fashion</div>
            <div class="seller-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
                Pusat Penjual
            </div>
            <h1>Kelola toko dan produkmu</h1>
            <p>Masuk untuk mengelola produk, melihat pesanan masuk, dan mengembangkan bisnis fashion lokalmu bersama kami.</p>

            <div class="seller-features">
                <div class="seller-feature">
                    <strong>Kelola Produk</strong>
                    Tambah, edit, dan atur stok produk
                </div>
                <div class="seller-feature">
                    <strong>Proses Pesanan</strong>
                    Terima dan proses pesanan masuk
                </div>
                <div class="seller-feature">
                    <strong>Statistik Toko</strong>
                    Pantau performa toko kamu
                </div>
                <div class="seller-feature">
                    <strong>UMKM Support</strong>
                    Banggakan karya lokal Indonesia
                </div>
            </div>
        </div>

        <div class="seller-form-side">
            <div class="seller-card">
                <a href="{{ route('home') }}" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Beranda
                </a>

                <h2>Masuk sebagai Penjual</h2>
                <p class="subtitle">Masuk untuk mengelola toko dan produkmu</p>

                <button type="button" class="btn btn-primary btn-block" style="margin-bottom: 18px;" onclick="fillLogin('penjual')">Masuk</button>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </form>

                <div class="seller-footer-text">
                    Belum punya akun? <a href="{{ route('login') }}">Daftar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(role) {
            const emails = {
                'penjual': 'penjual@lokal.id'
            };
            document.getElementById('email').value = emails[role] || '';
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
