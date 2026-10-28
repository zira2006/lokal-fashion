<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .checkout-page { padding: 35px 0 70px; }
        .checkout-heading { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin: 0 0 30px; }
        .checkout-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0; }
        .checkout-badge { display: inline-block; padding: 5px 12px; border-radius: 999px; background: #f1e3d4; color: #6d4c33; font-size: 11px; font-weight: 700; letter-spacing: 0.02em; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 380px; gap: 35px; align-items: start; }
        .checkout-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-bottom: 22px; }
        .checkout-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 8px; }
        .checkout-note { font-size: 13px; color: var(--muted); margin: 0 0 22px; }

        .address-card { display: block; border: 1.5px solid var(--line); border-radius: 12px; padding: 18px 20px; margin-bottom: 14px; cursor: pointer; transition: all 0.2s; }
        .address-card:hover { border-color: var(--brown); }
        .address-card.selected { border-color: var(--dark); background: #fdf6ee; box-shadow: 0 0 0 1px var(--dark) inset; }
        .address-card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 6px; }
        .address-card-name { font-weight: 600; font-size: 14px; color: var(--dark); }
        .address-card-phone { font-size: 12px; color: var(--muted); }
        .address-card-detail { font-size: 13px; color: var(--text); line-height: 1.6; margin-top: 5px; white-space: pre-line; }
        .address-card-location { font-size: 12px; color: var(--muted); margin-top: 3px; }
        .address-badge { font-size: 11px; background: var(--beige); color: var(--dark); padding: 3px 10px; border-radius: 12px; white-space: nowrap; }
        .address-links { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--line); flex-wrap: wrap; }
        .address-links a { font-size: 12px; font-weight: 600; color: var(--brown); }
        .address-links a:hover { color: var(--dark); text-decoration: underline; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-block { width: 100%; }

        .order-summary-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; color: var(--text); }
        .order-summary-row.total { border-top: 1px solid var(--line); margin-top: 10px; padding-top: 14px; font-weight: 700; font-size: 16px; color: var(--dark); }
        .order-item { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-bottom: 1px dashed var(--line); }
        .order-item:last-child { border-bottom: none; }
        .order-item-name { color: var(--text); }
        .order-item-variant { color: var(--muted); font-size: 11px; }
        .order-item-origin { color: var(--brown); font-size: 11px; margin-top: 2px; }

        @media (max-width: 860px) {
            .checkout-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 520px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container checkout-page">
        <div class="checkout-heading">
            <h1 class="checkout-title">Checkout</h1>
            @if($isDirectCheckout)
                <span class="checkout-badge">Beli Langsung</span>
            @endif
        </div>
        <p class="checkout-note" style="margin: -20px 0 25px;">
            @if($isDirectCheckout)
                Kamu sedang membeli langsung produk ini. Isi keranjangmu tidak berubah.
            @else
                Hanya produk yang kamu pilih di keranjang yang akan diproses.
            @endif
        </p>

        @if($addresses->isEmpty())
            {{-- Belum punya alamat: form alamat langsung tampil --}}
            <div class="checkout-grid">
                <div class="checkout-card" id="form-alamat">
                    <h2>Isi Alamat Pengiriman</h2>
                    <p class="checkout-note">Kamu belum punya alamat pengiriman. Isi alamat di bawah ini untuk melanjutkan pesanan.</p>

                    <form method="POST" action="{{ route('checkout.address') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nama_penerima">Nama Penerima</label>
                                <input type="text" id="nama_penerima" name="nama_penerima" value="{{ old('nama_penerima', auth()->user()->nama) }}" required autofocus>
                                @error('nama_penerima') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', auth()->user()->no_hp) }}" required>
                                @error('no_hp') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap</label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" required>{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="kota">Kota</label>
                                <input type="text" id="kota" name="kota" value="{{ old('kota') }}" required>
                                @error('kota') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="kode_pos">Kode Pos</label>
                                <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}" inputmode="numeric" maxlength="10">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Alamat &amp; Lanjutkan</button>
                    </form>
                </div>

                <div class="checkout-card">
                    <h2>Pesanan</h2>
                    @include('storefront.partials.order-summary')
                    <p class="checkout-note" style="margin: 18px 0 0;">Setelah alamat disimpan, kamu bisa langsung membuat pesanan.</p>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <div class="checkout-grid">
                    <div>
                        <div class="checkout-card">
                            <h2>Alamat Pengiriman</h2>

                            @foreach($addresses as $addr)
                                <label class="address-card {{ ($defaultAddress && $defaultAddress->id_alamat == $addr->id_alamat) ? 'selected' : '' }}">
                                    <input type="radio" name="id_alamat" value="{{ $addr->id_alamat }}"
                                        {{ ($defaultAddress && $defaultAddress->id_alamat == $addr->id_alamat) ? 'checked' : '' }}
                                        style="display:none;">
                                    <div class="address-card-header">
                                        <span class="address-card-name">{{ $addr->nama_penerima }}</span>
                                        @if($addr->is_default)
                                            <span class="address-badge">Utama</span>
                                        @endif
                                    </div>
                                    <div class="address-card-phone">{{ $addr->no_hp }}</div>
                                    <div class="address-card-detail">{{ trim($addr->alamat_lengkap) }}</div>
                                    <div class="address-card-location">
                                        {{ $addr->kota }}@if($addr->kode_pos) · {{ $addr->kode_pos }}@endif
                                    </div>
                                </label>
                            @endforeach

                            <div class="address-links">
                                <a href="{{ route('profile.addresses') }}">Kelola alamat</a>
                            </div>

                            @error('id_alamat')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="checkout-card">
                        <h2>Pesanan</h2>
                        @include('storefront.partials.order-summary')

                        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 22px;">Buat Pesanan</button>
                    </div>
                </div>
            </form>
        @endif

        @if($isDirectCheckout)
            <form id="cancelBuyNowForm" method="POST" action="{{ route('checkout.buyNow.cancel') }}" style="display: none;">
                @csrf
            </form>
        @endif
    </main>

    @include('layout.partials.footer')

    <script>
        document.querySelectorAll('.address-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
                this.closest('.address-card').classList.add('selected');
            });
        });
    </script>
</body>
</html>
