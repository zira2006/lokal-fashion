<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .dashboard-page { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .dashboard-sidebar { background: var(--dark); padding: 30px 0; }
        .dashboard-sidebar .brand { display: block; padding: 0 26px 28px; color: #f5ebe0; font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: 20px; }
        .dashboard-nav { display: flex; flex-direction: column; }
        .dashboard-nav a, .dashboard-nav .nav-logout { display: block; width: 100%; padding: 13px 26px; color: #d4c3b2; text-decoration: none; font-size: 14px; font-family: inherit; text-align: left; background: none; border: none; border-left: 3px solid transparent; cursor: pointer; transition: all 0.2s; }
        .dashboard-nav a:hover, .dashboard-nav a.active, .dashboard-nav .nav-logout:hover { color: #fff; background: rgba(255,255,255,.07); border-left-color: var(--brown); }
        .dashboard-main { padding: 35px 40px; }
        .greeting { font-size: 13px; color: var(--muted); margin: 0 0 22px; }
        .greeting strong { color: var(--dark); }
        .role-pill { display: inline-block; margin-left: 6px; padding: 3px 9px; border-radius: 20px; background: var(--beige); color: var(--dark); font-size: 10px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; }

        .dashboard-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .dashboard-header h1 { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 8px; }
        .dashboard-header p { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.6; }
        .dashboard-header p strong { color: var(--dark); }
        .dashboard-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .btn { display: inline-block; padding: 10px 20px; border: none; border-radius: 25px; font-size: 12px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 18px; margin-bottom: 35px; }
        .stat-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 22px; }
        .stat-card .stat-label { font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; color: var(--dark); }
        .stat-card .stat-icon { font-size: 22px; margin-bottom: 10px; }

        .section-title { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--dark); margin: 0 0 15px; }
        .panel-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .dashboard-table-wrap { overflow-x: auto; }
        .dashboard-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .dashboard-table th { background: var(--beige); padding: 13px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .dashboard-table td { padding: 13px 16px; border-top: 1px solid var(--line); font-size: 13px; vertical-align: middle; }
        .dashboard-table tr:hover td { background: #fdf9f4; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-terverifikasi, .status-selesai, .status-pembeli { background: #d9edda; color: #3d6b3e; }
        .status-menunggu, .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-ditolak, .status-batal { background: #fbe0dc; color: #a8442e; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-penjual { background: #f0e6d8; color: #79583f; }
        .status-admin { background: #e8e0f0; color: #5a3d99; }

        .empty-state { padding: 30px 16px; text-align: center; color: var(--muted); font-size: 13px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; }

        @media (max-width: 900px) {
            .panel-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 800px) {
            .dashboard-page { grid-template-columns: 1fr; }
            .dashboard-sidebar { padding: 15px 0; }
            .dashboard-nav { flex-direction: row; overflow-x: auto; }
            .dashboard-nav a, .dashboard-nav .nav-logout { width: auto; white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .dashboard-nav a:hover, .dashboard-nav a.active, .dashboard-nav .nav-logout:hover { border-bottom-color: var(--brown); }
            .dashboard-nav form { display: flex; }
            .dashboard-main { padding: 25px 18px; }
        }
        @media (max-width: 520px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="dashboard-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="dashboard-main">
            <p class="greeting">
                Halo, <strong>{{ Auth::user()->nama }}</strong>
                <span class="role-pill">{{ $role === 'admin' ? 'Admin' : 'Penjual' }}</span>
            </p>

            <div class="dashboard-top">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    @if($role === 'admin')
                        <p>Ringkasan aktivitas marketplace Lokal Fashion</p>
                    @else
                        <p>Ringkasan toko <strong>{{ $store->nama_toko }}</strong> dan pesanan terbaru</p>
                    @endif
                </div>

                @if($role === 'penjual')
                    <div class="dashboard-actions">
                        <a href="{{ route('seller.products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
                        <a href="{{ route('seller.orders') }}" class="btn btn-outline">Lihat Pesanan</a>
                    </div>
                @endif
            </div>

            @if($role === 'admin')
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

                <div class="panel-grid">
                    <div>
                        <h3 class="section-title">Pesanan Terbaru</h3>
                        <div class="dashboard-table-wrap">
                            <table class="dashboard-table">
                                <thead>
                                    <tr><th>Invoice</th><th>Pembeli</th><th>Total</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_orders'] as $order)
                                        <tr>
                                            <td><strong>{{ $order->no_invoice }}</strong></td>
                                            <td>{{ $order->user->nama ?? '-' }}</td>
                                            <td>Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                                            <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="empty-state">Belum ada pesanan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="section-title">Pengguna Terbaru</h3>
                        <div class="dashboard-table-wrap">
                            <table class="dashboard-table">
                                <thead>
                                    <tr><th>Nama</th><th>Role</th><th>Email</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_users'] as $user)
                                        <tr>
                                            <td><strong>{{ $user->nama }}</strong></td>
                                            <td><span class="status-badge status-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="empty-state">Belum ada pengguna.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🛍️</div>
                        <div class="stat-label">Produk Aktif</div>
                        <div class="stat-value">{{ $stats['active_products'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🆕</div>
                        <div class="stat-label">Pesanan Baru</div>
                        <div class="stat-value">{{ $stats['new_orders'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⏳</div>
                        <div class="stat-label">Diproses</div>
                        <div class="stat-value">{{ $stats['processing_orders'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🚚</div>
                        <div class="stat-label">Dikirim</div>
                        <div class="stat-value">{{ $stats['shipped_orders'] }}</div>
                    </div>
                </div>

                <div>
                    <h3 class="section-title">Pesanan Terbaru</h3>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr><th>Invoice</th><th>Pembeli</th><th>Produk</th><th>Status</th><th>Resi</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><strong>{{ $order->no_invoice }}</strong></td>
                                        <td>{{ $order->user->nama ?? '-' }}</td>
                                        <td>
                                            @foreach($order->items as $item)
                                                {{ $item->variant->product->nama_produk ?? '-' }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                        <td>{{ $order->resi ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="empty-state">Belum ada pesanan masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>
</body>
</html>
