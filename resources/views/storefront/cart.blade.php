
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .cart-page { padding: 35px 0 70px; }
        .cart-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 30px; }

        .cart-table-wrap { overflow-x: auto; }
        .cart-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .cart-table th { background: var(--beige); padding: 14px 18px; text-align: left; font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .cart-table td { padding: 16px 18px; border-top: 1px solid var(--line); font-size: 14px; vertical-align: middle; }

        .cart-select { width: 48px; text-align: center !important; }
        .cart-select input { width: 17px; height: 17px; accent-color: var(--dark); cursor: pointer; }
        .cart-row { transition: background 0.15s; }
        .cart-row.is-selected td { background: #fdf9f4; }

        .cart-hint { margin: -18px 0 18px; font-size: 13px; color: var(--muted); }

        .cart-product { display: flex; align-items: center; gap: 14px; }
        .cart-product-img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; background: var(--beige); flex-shrink: 0; }
        .cart-product-name { font-weight: 600; color: var(--dark); }
        .cart-product-store { font-size: 12px; color: var(--muted); }
        .cart-product-origin { font-size: 11px; color: var(--brown); margin-top: 2px; }

        .variant-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .variant-tag { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .variant-tag.color { background: #f1e3d4; color: #6d4c33; }
        .variant-tag.size { background: #f7f2ea; color: #6d5a49; }

        .qty-control { display: inline-flex; align-items: center; border: 1.5px solid var(--line); border-radius: 20px; overflow: hidden; }
        .qty-control button { width: 32px; height: 32px; border: none; background: var(--white); cursor: pointer; font-size: 15px; color: var(--dark); }
        .qty-control button:hover { background: var(--beige); }
        .qty-control span { min-width: 30px; text-align: center; font-size: 13px; font-weight: 600; }

        .cart-remove { color: #a8442e; font-size: 12px; cursor: pointer; background: none; border: none; font-family: inherit; }
        .cart-remove:hover { text-decoration: underline; }

        .cart-summary { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-top: 25px; }
        .cart-summary h3 { font-family: 'Playfair Display', serif; font-size: 18px; color: var(--dark); margin: 0 0 18px; }
        .cart-summary-row { display: flex; justify-content: space-between; font-size: 14px; padding: 7px 0; color: var(--text); }
        .cart-summary-row.total { border-top: 1px solid var(--line); margin-top: 10px; padding-top: 14px; font-weight: 700; font-size: 17px; color: var(--dark); }

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-primary:disabled { background: #cdbbaa; cursor: not-allowed; box-shadow: none; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-block { width: 100%; }
        .cart-actions { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }

        .cart-empty { text-align: center; padding: 70px 20px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; }
        .cart-empty p { color: var(--muted); font-size: 15px; margin-bottom: 22px; }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container cart-page">
        <h1 class="cart-title">Keranjang Belanja</h1>

        @if($errors->any())
            <div class="alert alert-error" role="alert" style="margin: 0 0 18px;">
                {{ $errors->first('selected') ?: $errors->first() }}
            </div>
        @endif

        @if(empty($items))
            <div class="cart-empty">
                <p>Keranjangmu masih kosong.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        @else
            <p class="cart-hint">
                Centang produk yang ingin di-checkout. Produk yang tidak dicentang tetap tersimpan di keranjang.
                Setiap warna dan ukuran ditampilkan sebagai baris terpisah.
            </p>

            {{-- Form terpisah; checkbox di tabel terhubung lewat atribut form --}}
            <form id="checkoutForm" method="POST" action="{{ route('cart.checkout') }}">
                @csrf
            </form>

            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="cart-select">
                                <input type="checkbox" id="selectAll" checked aria-label="Pilih semua produk">
                            </th>
                            <th>Produk</th>
                            <th>Warna / Ukuran</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="cart-row is-selected" data-id-varian="{{ $item['variant']->id_varian }}"
                                data-qty="{{ $item['jumlah'] }}" data-subtotal="{{ $item['subtotal'] }}">
                                <td class="cart-select">
                                    <input type="checkbox" name="selected[]" value="{{ $item['variant']->id_varian }}"
                                           form="checkoutForm" class="item-check" checked
                                           aria-label="Pilih {{ $item['variant']->product->nama_produk }}">
                                </td>
                                <td>
                                    <div class="cart-product" data-price="{{ $item['variant']->harga }}">
                                        <img src="{{ $item['variant']->gambar_url ?: $item['variant']->product->gambar_url }}" alt="{{ $item['variant']->product->nama_produk }}" class="cart-product-img"
                                             onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                                        <div>
                                            <div class="cart-product-name">{{ $item['variant']->product->nama_produk }}</div>
                                            <div class="cart-product-store">{{ $item['variant']->product->store?->nama_toko ?? '' }}</div>
                                            @if($item['variant']->product->asal)
                                                <div class="cart-product-origin">Asal: {{ $item['variant']->product->asal }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="variant-tags">
                                        <span class="variant-tag color">Warna: {{ $item['variant']->warna ?: '-' }}</span>
                                        <span class="variant-tag size">
                                            {{ $item['variant']->ukuran && $item['variant']->ukuran !== '-' ? 'Ukuran: '.$item['variant']->ukuran : 'All size' }}
                                        </span>
                                    </div>
                                </td>
                                <td>Rp{{ number_format((float) $item['variant']->harga, 0, ',', '.') }}</td>
                                <td>
                                    <div class="qty-control">
                                        <form method="POST" action="{{ route('cart.update') }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id_varian" value="{{ $item['variant']->id_varian }}">
                                            <input type="hidden" name="jumlah" value="{{ max(1, $item['jumlah'] - 1) }}">
                                            <button type="submit">−</button>
                                        </form>
                                        <span>{{ $item['jumlah'] }}</span>
                                        <form method="POST" action="{{ route('cart.update') }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id_varian" value="{{ $item['variant']->id_varian }}">
                                            <input type="hidden" name="jumlah" value="{{ $item['jumlah'] + 1 }}">
                                            <button type="submit">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td><strong>Rp{{ number_format((float) $item['subtotal'], 0, ',', '.') }}</strong></td>
                                <td>
                                    <form method="POST" action="{{ route('cart.remove') }}">
                                        @csrf
                                        <input type="hidden" name="id_varian" value="{{ $item['variant']->id_varian }}">
                                        <button type="submit" class="cart-remove">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h3>Ringkasan</h3>
                <div class="cart-summary-row">
                    <span>Produk dipilih</span>
                    <span><span id="selectedCount">{{ count($items) }}</span> dari {{ count($items) }}</span>
                </div>
                <div class="cart-summary-row">
                    <span>Total Item</span>
                    <span id="summaryQty">{{ collect($items)->sum('jumlah') }}</span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total Bayar</span>
                    <span id="summaryTotal">Rp{{ number_format((float) $total, 0, ',', '.') }}</span>
                </div>
                <div class="cart-actions">
                    <a href="{{ route('catalog') }}" class="btn btn-outline" style="flex:1;">Lanjut Belanja</a>
                    <button type="submit" form="checkoutForm" id="checkoutBtn" class="btn btn-primary" style="flex:1;">
                        Checkout ({{ count($items) }})
                    </button>
                </div>
            </div>
        @endif
    </main>

    @include('layout.partials.footer')

    @if(!empty($items))
        <script>
            (function () {
                const checkboxes = Array.from(document.querySelectorAll('.item-check'));
                const selectAll = document.getElementById('selectAll');
                const selectedCount = document.getElementById('selectedCount');
                const summaryQty = document.getElementById('summaryQty');
                const summaryTotal = document.getElementById('summaryTotal');
                const checkoutBtn = document.getElementById('checkoutBtn');
                const rupiah = new Intl.NumberFormat('id-ID');

                function syncSelection() {
                    let count = 0;
                    let qty = 0;
                    let total = 0;

                    checkboxes.forEach(function (checkbox) {
                        const row = checkbox.closest('.cart-row');
                        row.classList.toggle('is-selected', checkbox.checked);

                        if (checkbox.checked) {
                            count++;
                            qty += parseInt(row.dataset.qty, 10) || 0;
                            total += parseFloat(row.dataset.subtotal) || 0;
                        }
                    });

                    selectedCount.textContent = count;
                    summaryQty.textContent = qty;
                    summaryTotal.textContent = 'Rp' + rupiah.format(total);

                    checkoutBtn.disabled = count === 0;
                    checkoutBtn.textContent = count === 0 ? 'Pilih produk' : 'Checkout (' + count + ')';

                    selectAll.checked = count === checkboxes.length;
                    selectAll.indeterminate = count > 0 && count < checkboxes.length;
                }

                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                    syncSelection();
                });

                checkboxes.forEach(function (checkbox) {
                    checkbox.addEventListener('change', syncSelection);
                });

                syncSelection();
            })();
        </script>
    @endif
</body>
</html>
