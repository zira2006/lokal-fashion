
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan | Pusat Penjual - Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .seller-page { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .seller-sidebar { background: var(--dark); padding: 30px 0; }
        .seller-sidebar .brand { display: block; padding: 0 26px 28px; color: #f5ebe0; font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: 20px; }
        .seller-nav { display: flex; flex-direction: column; }
        .seller-nav a { padding: 13px 26px; color: #d4c3b2; text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
        .seller-nav a:hover, .seller-nav a.active { color: #fff; background: rgba(255,255,255,.07); border-left-color: var(--brown); }
        .seller-main { padding: 35px 40px; }
        .seller-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 28px; }

        .seller-table-wrap { overflow-x: auto; }
        .seller-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .seller-table th { background: var(--beige); padding: 14px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .seller-table td { padding: 14px 16px; border-top: 1px solid var(--line); font-size: 13px; vertical-align: middle; }
        .seller-table tr:hover td { background: #fdf9f4; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-selesai { background: #d9edda; color: #3d6b3e; }
        .status-batal { background: #fbe0dc; color: #a8442e; }

        .btn { display: inline-block; padding: 9px 18px; border: none; border-radius: 25px; font-size: 12px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }

        .seller-empty { text-align: center; padding: 60px 20px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; color: var(--muted); }

        .flash { padding: 13px 18px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .flash-success { background: #e5f2e5; border: 1px solid #b9d8b9; color: #3d6b3e; }
        .flash-error { background: #f8e7e3; border: 1px solid #e7c0b7; color: #8e3624; }

        @media (max-width: 800px) {
            .seller-page { grid-template-columns: 1fr; }
            .seller-sidebar { padding: 15px 0; }
            .seller-nav { flex-direction: row; overflow-x: auto; }
            .seller-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .seller-nav a:hover, .seller-nav a.active { border-bottom-color: var(--brown); }
            .seller-main { padding: 25px 18px; }
        }
    </style>
</head>
<body>
    <div class="seller-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="seller-main">
            <div class="seller-header">
                <h1>Pesanan</h1>
            </div>

            <p style="font-size:12px; color:var(--muted); margin-bottom:16px;">
                Untuk pesanan COD, status pembayaran otomatis menjadi <strong>Lunas</strong>
                saat pesanan diselesaikan.
            </p>

            @if(session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="flash flash-error">{{ $errors->first() }}</div>
            @endif

            @if($orders->isNotEmpty())
                <div class="seller-table-wrap">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Produk</th>
                                <th>Penerima</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong style="color:var(--dark);">{{ $order->no_invoice }}</strong>
                                        <div style="font-size:11px; color:var(--muted); margin-top:3px;">{{ $order->paymentMethodLabel() }} • {{ $order->paymentStatusLabel() }}</div>
                                    </td>
                                    <td>
                                        @foreach($order->items as $item)
                                            {{ $item->variant->product->nama_produk }} ({{ $item->variant->warna }}, {{ $item->variant->ukuran }}) x{{ $item->jumlah }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $order->address->nama_penerima }}</td>
                                    <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('seller.orders.update', $order->no_invoice) }}" style="display:flex; flex-direction:column; gap:8px; min-width:320px;">
                                            @csrf
                                            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                                <select name="status" style="padding:8px 12px; border:1px solid var(--line); border-radius:8px; font-size:12px; font-family:inherit;">
                                                    <option value="baru" {{ $order->status == 'baru' ? 'selected' : '' }}>Baru</option>
                                                    <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                                    <option value="dikirim" {{ $order->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                                <button type="submit" name="action" value="simpan" class="btn btn-outline btn-sm">Simpan Status</button>
                                            </div>
                                            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                                <input type="text" name="kurir" placeholder="Kurir" value="{{ $order->kurir }}" style="padding:8px 12px; border:1px solid var(--line); border-radius:8px; font-size:12px; width:80px; font-family:inherit;">
                                                <input type="text" name="resi" placeholder="No. Resi" value="{{ $order->resi }}" style="padding:8px 12px; border:1px solid var(--line); border-radius:8px; font-size:12px; width:100px; font-family:inherit;">
                                                <button type="submit" name="action" value="kirim_resi" class="btn btn-primary btn-sm" style="white-space:nowrap;">Kirim No. Resi ke Pembeli</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="seller-empty">
                    <p>Belum ada pesanan masuk.</p>
                </div>
            @endif
        </main>
    </div>
</body>
</html>
