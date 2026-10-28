
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .orders-page { padding: 35px 0 70px; }
        .orders-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 28px; }
        .orders-tabs { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
        .order-tab { padding: 10px 22px; border: 1.5px solid var(--line); border-radius: 25px; font-size: 13px; font-weight: 600; color: var(--muted); text-decoration: none; transition: all 0.2s; background: var(--white); }
        .order-tab:hover { border-color: var(--brown); color: var(--dark); }
        .order-tab.active { background: var(--dark); color: #fff; border-color: var(--dark); }
        .order-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 22px 26px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .order-card-invoice { font-weight: 700; font-size: 15px; color: var(--dark); }
        .order-card-status { font-size: 12px; padding: 5px 14px; border-radius: 20px; font-weight: 600; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-selesai { background: #d9edda; color: #3d6b3e; }
        .status-batal { background: #fbe0dc; color: #a8442e; }
        .order-card-items { font-size: 13px; color: var(--text); margin-top: 6px; }
        .order-card-total { font-weight: 700; font-size: 15px; color: var(--dark); }
        .btn { display: inline-block; padding: 10px 22px; border: none; border-radius: 25px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .orders-empty { text-align: center; padding: 60px 20px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; color: var(--muted); }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container orders-page">
        <h1 class="orders-title">Pesanan Saya</h1>

        <div class="orders-tabs">
            <a href="{{ route('orders.index', ['tab' => 'semua']) }}" class="order-tab {{ $tab == 'semua' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('orders.index', ['tab' => 'baru']) }}" class="order-tab {{ $tab == 'baru' ? 'active' : '' }}">Baru</a>
            <a href="{{ route('orders.index', ['tab' => 'diproses']) }}" class="order-tab {{ $tab == 'diproses' ? 'active' : '' }}">Diproses</a>
            <a href="{{ route('orders.index', ['tab' => 'dikirim']) }}" class="order-tab {{ $tab == 'dikirim' ? 'active' : '' }}">Dikirim</a>
            <a href="{{ route('orders.index', ['tab' => 'selesai']) }}" class="order-tab {{ $tab == 'selesai' ? 'active' : '' }}">Selesai</a>
        </div>

        @forelse($orders as $order)
            <div class="order-card">
                <div>
                    <div class="order-card-invoice">
                        <a href="{{ route('orders.show', $order->no_invoice) }}" style="color:inherit; text-decoration:none;">{{ $order->no_invoice }}</a>
                        <span class="order-card-status status-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="order-card-items">
                        @foreach($order->items as $item)
                            {{ $item->variant->product->nama_produk }} ({{ $item->variant->warna }}, {{ $item->variant->ukuran }}) x{{ $item->jumlah }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                    <div class="order-card-items" style="font-size:12px; color:var(--muted);">
                        Pembayaran: {{ $order->paymentMethodLabel() }} • {{ $order->paymentStatusLabel() }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="order-card-total">Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</div>
                    <a href="{{ route('orders.show', $order->no_invoice) }}" class="btn btn-outline btn-sm" style="margin-top: 10px;">Detail</a>
                </div>
            </div>
        @empty
            <div class="orders-empty">
                <p>Tidak ada pesanan dengan status ini.</p>
            </div>
        @endforelse
    </main>

    @include('layout.partials.footer')
</body>
</html>
