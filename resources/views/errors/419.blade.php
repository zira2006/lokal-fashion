<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Kedaluwarsa | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f7f1e8;
            --white: #fffdf9;
            --brown: #8b5e3c;
            --dark: #3f2d20;
            --muted: #8a7767;
            --line: #e7dccf;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: var(--cream);
            color: var(--dark);
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
        }

        .session-card {
            width: 100%;
            max-width: 520px;
            padding: 42px 38px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 18px 50px rgba(91, 58, 35, .10);
            text-align: center;
        }

        .brand {
            display: inline-block;
            margin-bottom: 26px;
            color: var(--dark);
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
        }

        .session-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f3e7da;
            color: var(--brown);
        }

        h1 {
            margin: 0 0 10px;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
        }

        p {
            margin: 0 auto 26px;
            max-width: 380px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 145px;
            padding: 12px 22px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }

        .btn-primary {
            background: var(--dark);
            color: #fff;
        }

        .btn-primary:hover { background: #6d5340; }

        .btn-outline {
            background: transparent;
            border-color: var(--line);
            color: var(--dark);
        }

        .btn-outline:hover {
            border-color: var(--brown);
            color: var(--brown);
        }

        @media (max-width: 480px) {
            .session-card { padding: 32px 22px; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="session-card">
        <a href="{{ route('home') }}" class="brand">Lokal Fashion</a>

        <div class="session-icon" aria-hidden="true">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
            </svg>
        </div>

        <h1>Sesi kamu sudah berakhir</h1>
        <p>
            Halaman ini sudah terlalu lama terbuka atau token keamanannya sudah kedaluwarsa.
            Silakan kembali dan coba lagi. Jika kamu baru saja menekan tombol Keluar,
            proses logout tetap aman.
        </p>

        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('home') }}'; }">
                Kembali
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline">Ke Beranda</a>
        </div>
    </main>
</body>
</html>
