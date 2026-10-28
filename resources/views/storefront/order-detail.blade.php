
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail {{ $order->no_invoice }} | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .order-detail-page { padding: 35px 0 70px; }
        .order-detail-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 28px; }
        .order-detail-grid { display: grid; grid-template-columns: 1fr 380px; gap: 35px; align-items: start; }
        .detail-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-bottom: 22px; }
        .detail-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 18px; }
        .detail-row { display: flex; justify-content: space-between; font-size: 14px; padding: 7px 0; }
        .detail-row .label { color: var(--muted); }
        .detail-origin { display: block; font-size: 11px; color: var(--muted); margin-top: 3px; }
        .status-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-baru { background: #e8f0fe; color: #2c5aa0; }
        .status-diproses { background: #fdf0d5; color: #8a6d1a; }
        .status-dikirim { background: #e6e0f8; color: #5a3d99; }
        .status-selesai { background: #d9edda; color: #3d6b3e; }
        .status-batal { background: #fbe0dc; color: #a8442e; }

        .review-form-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-top: 25px; }
        .review-form-card h3 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 18px; }
        .star-rating { display: flex; gap: 6px; margin-bottom: 16px; }
        .star-rating button { background: none; border: none; font-size: 28px; color: #d4c3b2; cursor: pointer; transition: color 0.15s; padding: 0; line-height: 1; }
        .star-rating button.active { color: #d4a04a; }
        .review-form-card textarea { width: 100%; padding: 13px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; min-height: 100px; resize: vertical; outline: none; }
        .review-form-card textarea:focus { border-color: var(--brown); }

        .btn { display: inline-block; padding: 12px 26px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container order-detail-page">
        <h1 class="order-detail-title">Detail {{ $order->no_invoice }}</h1>

        <div class="order-detail-grid">
            <div>
                <!-- INFO PENGIRIMAN -->
                <div class="detail-card">
                    <h2>Informasi Pengiriman</h2>
                    <div class="detail-row"><span class="label">Status</span> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div>
                    <div class="detail-row"><span class="label">Kurir</span> <span>{{ $order->kurir ?: '-' }}</span></div>
                    <div class="detail-row"><span class="label">Resi</span> <span>{{ $order->resi ?: '-' }}</span></div>
                    <div class="detail-row"><span class="label">Penerima</span> <span>{{ $order->address->nama_penerima }}</span></div>
                    <div class="detail-row"><span class="label">Alamat</span> <span>{{ $order->address->alamat_lengkap }}, {{ $order->address->kota }}</span></div>
                </div>

                @if($order->status === 'dikirim')
                    <div class="detail-card">
                        <h2>Konfirmasi Penerimaan</h2>
                        <p style="font-size:13px; color:var(--muted); line-height:1.7; margin-bottom:16px;">
                            Klik tombol di bawah jika barang sudah kamu terima.
                            @if($order->status_pembayaran !== 'lunas')
                                Status pembayaran akan ditandai <strong>Lunas</strong> setelah kamu mengonfirmasi.
                            @endif
                        </p>
                        <form method="POST" action="{{ route('orders.receive', $order->no_invoice) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Pesanan Sudah Diterima</button>
                        </form>
                    </div>
                @endif

                <!-- ULASAN -->
                @if($order->status === 'selesai' && !$order->review)
                    <div class="review-form-card">
                        <h3>Beri Ulasan</h3>
                        <form method="POST" action="{{ route('orders.review', $order->no_invoice) }}">
                            @csrf
                            <div class="star-rating" id="starRating">
                                <button type="button" onclick="setRating(1)">★</button>
                                <button type="button" onclick="setRating(2)">★</button>
                                <button type="button" onclick="setRating(3)">★</button>
                                <button type="button" onclick="setRating(4)">★</button>
                                <button type="button" onclick="setRating(5)">★</button>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="5">

                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:var(--dark); margin-bottom:7px;">Pilih Produk</label>
                                <select name="id_produk" style="width:100%; padding:12px 15px; border:1px solid var(--line); border-radius:10px; font-size:14px; font-family:inherit; background:#fff;">
                                    @foreach($order->items as $item)
                                        <option value="{{ $item->variant->product->id_produk }}">{{ $item->variant->product->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <textarea name="komentar" placeholder="Tulis komentar..."></textarea>

                            @error('rating')
                                <p style="color:#a8442e; font-size:12px; margin-top:8px;">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="btn btn-primary" style="margin-top:16px;">Kirim Ulasan</button>
                        </form>
                    </div>
                @elseif($order->review)
                    <div class="detail-card">
                        <h2>Ulasan Kamu</h2>
                        <div style="color:#d4a04a; font-size:18px; margin-bottom:8px;">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $order->review->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <p style="font-size:14px; color:var(--text);">{{ $order->review->komentar ?: 'Tidak ada komentar.' }}</p>
                    </div>
                @endif
            </div>

            <!-- RINGKASAN -->
            <div class="detail-card">
                <h2>Pesanan</h2>
                @foreach($order->items as $item)
                    <div class="detail-row">
                        <span>
                            {{ $item->variant->product->nama_produk }} ({{ $item->variant->warna }}, {{ $item->variant->ukuran }}) x{{ $item->jumlah }}
                            @if($item->variant->product->asal)
                                <span class="detail-origin">Asal: {{ $item->variant->product->asal }}</span>
                            @endif
                        </span>
                        <span>Rp{{ number_format((float) $item->harga * $item->jumlah, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="detail-row" style="border-top:1px solid var(--line); margin-top:10px; padding-top:14px; font-weight:700; font-size:17px; color:var(--dark);">
                    <span>Total</span>
                    <span>Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row" style="border-top:1px dashed var(--line); margin-top:8px; padding-top:12px;">
                    <span class="label">Metode Pembayaran</span>
                    <span>{{ $order->paymentMethodLabel() }}</span>
                </div>
                @if($order->payment_account)
                    <div class="detail-row">
                        <span class="label">Akun</span>
                        <span>{{ $order->payment_account }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="label">Status Pembayaran</span>
                    <span>{{ $order->paymentStatusLabel() }}</span>
                </div>
            </div>
        </div>
    </main>

    @include('layout.partials.footer')

    <script>
        function setRating(n) {
            document.getElementById('ratingInput').value = n;
            document.querySelectorAll('#starRating button').forEach((btn, i) => {
                btn.classList.toggle('active', i < n);
            });
        }
    </script>
</body>
</html>
