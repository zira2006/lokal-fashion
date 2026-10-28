
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .admin-page { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .admin-sidebar { background: var(--dark); padding: 30px 0; }
        .admin-sidebar .brand { display: block; padding: 0 26px 28px; color: #f5ebe0; font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: 20px; }
        .admin-nav { display: flex; flex-direction: column; }
        .admin-nav a { padding: 13px 26px; color: #d4c3b2; text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
        .admin-nav a:hover, .admin-nav a.active { color: #fff; background: rgba(255,255,255,.07); border-left-color: var(--brown); }
        .admin-main { padding: 35px 40px; }
        .admin-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 8px; }
        .admin-header p { font-size: 13px; color: var(--muted); margin: 0 0 30px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 18px; margin-bottom: 35px; }
        .stat-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 22px; }
        .stat-card .stat-label { font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; color: var(--dark); }
        .stat-card .stat-icon { font-size: 22px; margin-bottom: 10px; }

        .admin-table-wrap { overflow-x: auto; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .admin-table th { background: var(--beige); padding: 13px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-table td { padding: 13px 16px; border-top: 1px solid var(--line); font-size: 13px; vertical-align: middle; }
        .admin-table tr:hover td { background: #fdf9f4; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-terverifikasi { background: #d9edda; color: #3d6b3e; }
        .status-menunggu { background: #fdf0d5; color: #8a6d1a; }
        .status-ditolak { background: #fbe0dc; color: #a8442e; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-selesai { background: #d9edda; color: #3d6b3e; }
        .status-pembeli { background: #e0f0e0; color: #3d6b3e; }
        .status-penjual { background: #f0e6d8; color: #79583f; }
        .status-admin { background: #e8e0f0; color: #5a3d99; }

        .btn { display: inline-block; padding: 9px 18px; border: none; border-radius: 25px; font-size: 12px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-sm { padding: 7px 14px; font-size: 11px; }

        .greeting { font-size: 13px; color: var(--muted); margin-bottom: 25px; }
        .greeting strong { color: var(--dark); }

        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 800px) {
            .admin-page { grid-template-columns: 1fr; }
            .admin-sidebar { padding: 15px 0; }
            .admin-nav { flex-direction: row; overflow-x: auto; }
            .admin-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .admin-nav a:hover, .admin-nav a.active { border-bottom-color: var(--brown); }
            .admin-main { padding: 25px 18px; }
        }
        @media (max-width: 520px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="admin-main">
            <p class="greeting">Halo, <strong>{{ Auth::user()->nama }}</strong> (Admin)</p>

            <div class="admin-header">
                <h1>Dashboard</h1>
                <p>Ringkasan aktivitas marketplace Lokal Fashion</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-label">Total Pengguna</div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-label">Toko</div>
                    <div class="stat-value">{{ $stats['total_stores'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-label">Produk</div>
                    <div class="stat-value">{{ $stats['total_products'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🛒</div>
                    <div class="stat-label">Pesanan</div>
                    <div class="stat-value">{{ $stats['total_orders'] }}</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:25px;">
                <div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:17px; color:var(--dark); margin:0 0 15px;">Pesanan Terbaru</h3>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead><tr><th>Invoice</th><th>Pembeli</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($stats['recent_orders'] as $order)
                                    <tr>
                                        <td><strong>{{ $order->no_invoice }}</strong></td>
                                        <td>{{ $order->user->nama }}</td>
                                        <td>Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                                        <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="text-align:center; color:var(--muted);">Belum ada pesanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:17px; color:var(--dark); margin:0 0 15px;">Pengguna Terbaru</h3>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead><tr><th>Nama</th><th>Role</th><th>Email</th></tr></thead>
                            <tbody>
                                @forelse($stats['recent_users'] as $user)
                                    <tr>
                                        <td><strong>{{ $user->nama }}</strong></td>
                                        <td><span class="status-badge status-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" style="text-align:center; color:var(--muted);">Belum ada pengguna.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
