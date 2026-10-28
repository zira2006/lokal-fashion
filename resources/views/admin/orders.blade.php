
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Pesanan | Admin - Lokal Fashion</title>
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
        .admin-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 25px; }

        .admin-table-wrap { overflow-x: auto; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .admin-table th { background: var(--beige); padding: 13px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-table td { padding: 13px 16px; border-top: 1px solid var(--line); font-size: 13px; vertical-align: middle; }
        .admin-table tr:hover td { background: #fdf9f4; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-selesai { background: #d9edda; color: #3d6b3e; }
        .status-batal { background: #fbe0dc; color: #a8442e; }

        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 30px; }
        .pagination a, .pagination span { padding: 9px 15px; border: 1px solid var(--line); border-radius: 8px; font-size: 13px; color: var(--text); text-decoration: none; background: var(--white); }
        .pagination .active { background: var(--dark); color: #fff; border-color: var(--dark); }

        .greeting { font-size: 13px; color: var(--muted); margin-bottom: 25px; }
        .greeting strong { color: var(--dark); }

        @media (max-width: 800px) {
            .admin-page { grid-template-columns: 1fr; }
            .admin-sidebar { padding: 15px 0; }
            .admin-nav { flex-direction: row; overflow-x: auto; }
            .admin-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .admin-nav a:hover, .admin-nav a.active { border-bottom-color: var(--brown); }
            .admin-main { padding: 25px 18px; }
        }
    </style>
</head>
<body>
    <div class="admin-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="admin-main">
            <p class="greeting">Halo, <strong>{{ Auth::user()->nama }}</strong> (Admin)</p>
            <div class="admin-header">
                <h1>Semua Pesanan</h1>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Invoice</th><th>Pembeli</th><th>Penerima</th><th>Total</th><th>Pembayaran</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->no_invoice }}</strong></td>
                                <td>{{ $order->user->nama }}</td>
                                <td>{{ $order->address->nama_penerima ?? '-' }}</td>
                                <td>Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    {{ $order->paymentMethodLabel() }}
                                    <div style="font-size:11px; color:var(--muted); margin-top:3px;">{{ $order->paymentStatusLabel() }}</div>
                                </td>
                                <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center; color:var(--muted);">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $orders->links() }}
            </div>
        </main>
    </div>
</body>
</html>
