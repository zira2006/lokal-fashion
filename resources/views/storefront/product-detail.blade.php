
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->nama_produk }} | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .product-detail-page {
            padding: 35px 0 70px;
        }

        .breadcrumb {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .breadcrumb a {
            color: var(--brown);
            text-decoration: none;
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 60px;
        }

        .product-gallery-main {
            position: relative;
            width: 100%;
            aspect-ratio: 3 / 4;
            border-radius: 14px;
            overflow: hidden;
            background: var(--beige);
            margin-bottom: 14px;
        }

        .product-stock-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: rgba(89, 70, 55, 0.92);
            color: #fff8ef;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 7px 14px;
            border-radius: 20px;
        }

        .product-gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-gallery-thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .product-gallery-thumbs .thumb {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            background: var(--beige);
            padding: 0;
        }

        .product-gallery-thumbs .thumb.active {
            border-color: var(--brown);
        }

        .product-gallery-thumbs .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: var(--dark);
            margin: 0 0 10px;
            line-height: 1.3;
        }

        .product-meta {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .product-origin {
            color: var(--brown);
        }

        .product-price {
            font-size: 26px;
            font-weight: 700;
            color: var(--brown);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .variant-section {
            margin-bottom: 20px;
        }

        .variant-section h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 10px;
        }

        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .variant-btn {
            padding: 9px 18px;
            border: 1.5px solid var(--line);
            border-radius: 25px;
            background: var(--white);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            color: var(--text);
        }

        .variant-btn:hover {
            border-color: var(--brown);
        }

        .variant-btn.selected {
            border-color: var(--dark);
            border-width: 3px;
            font-weight: 700;
        }

        .qty-selector {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1.5px solid var(--line);
            border-radius: 25px;
            overflow: hidden;
            width: fit-content;
        }

        .qty-selector button {
            width: 40px;
            height: 40px;
            border: none;
            background: var(--white);
            font-size: 18px;
            cursor: pointer;
            color: var(--dark);
            transition: background 0.15s;
        }

        .qty-selector button:hover {
            background: var(--beige);
        }

        .qty-selector input {
            width: 50px;
            text-align: center;
            border: none;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        .stock-info {
            font-size: 13px;
            color: var(--muted);
            margin: 8px 0 20px;
        }

        .product-actions {
            display: flex;
            gap: 14px;
            margin-bottom: 30px;
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

        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--dark);
            color: var(--dark);
        }

        .btn-outline:hover {
            background: var(--dark);
            color: #fff;
        }

        .btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
        }

        .product-description {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 28px;
        }

        .product-description h3 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--dark);
            margin: 0 0 14px;
        }

        .product-description p {
            font-size: 14px;
            color: var(--text);
            line-height: 1.7;
        }

        /* ULASAN */
        .reviews-section {
            margin-top: 50px;
        }

        .reviews-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .reviews-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--dark);
            margin: 0;
        }

        .reviews-avg {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .reviews-avg .stars {
            color: #d4a04a;
            font-size: 16px;
        }

        .review-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 14px;
        }

        .review-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .review-card-user {
            font-weight: 600;
            font-size: 14px;
            color: var(--dark);
        }

        .review-card-rating {
            color: #d4a04a;
            font-size: 14px;
        }

        .review-card-text {
            font-size: 13px;
            color: var(--text);
            line-height: 1.6;
        }

        .no-reviews {
            text-align: center;
            padding: 40px;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 800px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>
<body>

    @include('layout.partials.header')

    <main class="container product-detail-page">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a> &rsaquo;
            <a href="{{ route('catalog') }}">Katalog</a> &rsaquo;
            <span>{{ $product->nama_produk }}</span>
        </nav>

        @php
            $galleryImages = $product->variants
                ->filter(fn ($variant) => filled($variant->gambar))
                ->map(fn ($variant) => [
                    'url' => $variant->gambar_url,
                    'warna' => $variant->warna,
                ])
                ->unique('url')
                ->values();

            if (filled($product->gambar) && ! $galleryImages->contains('url', $product->gambar_url)) {
                $galleryImages->push([
                    'url' => $product->gambar_url,
                    'warna' => null,
                ]);
            }

            $mainImageUrl = $galleryImages->first()['url'] ?? $product->gambar_url;
            $defaultImageUrl = filled($product->gambar) ? $product->gambar_url : $mainImageUrl;
            $colorImages = $galleryImages
                ->whereNotNull('warna')
                ->groupBy('warna')
                ->map(fn ($images) => $images->pluck('url')->values())
                ->toArray();
        @endphp

        <div class="product-detail-grid">
            <!-- GALERI -->
            <div>
                <div class="product-gallery-main">
                    <img id="mainImage" src="{{ $mainImageUrl }}" alt="{{ $product->nama_produk }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                    @if($product->totalStok() < 1)
                        <span class="product-stock-badge">Stok Habis</span>
                    @endif
                </div>

                @if($galleryImages->count() > 1)
                    <div class="product-gallery-thumbs" id="productThumbs">
                        @foreach($galleryImages as $image)
                            <button type="button"
                                    class="thumb {{ $image['url'] === $mainImageUrl ? 'active' : '' }}"
                                    data-src="{{ $image['url'] }}"
                                    data-color="{{ $image['warna'] }}"
                                    onclick="selectGalleryImage(this)"
                                    title="{{ $image['warna'] ? 'Warna '.$image['warna'] : 'Gambar utama' }}">
                                <img src="{{ $image['url'] }}"
                                     alt="{{ $product->nama_produk }}{{ $image['warna'] ? ' warna '.$image['warna'] : '' }}"
                                     onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- INFO -->
            <div class="product-info">
                @php
                    $initialVariant = $product->varian_tersedia ?? $product->variants->first();
                    $colors = $product->variants->pluck('warna')->unique()->values();
                    $sizes = $product->variants->pluck('ukuran')->unique()->values();
                    $initialColor = $initialVariant?->warna ?? '';
                    $initialSize = $initialVariant?->ukuran ?? '';
                @endphp

                <h1>{{ $product->nama_produk }}</h1>
                <p class="product-meta">
                    Toko: {{ $product->store?->nama_toko ?? '-' }}
                    @if($product->store && $product->store->status_verifikasi === 'terverifikasi')
                        <span style="color: #6a8f5a;">(UMKM Terverifikasi)</span>
                    @endif
                    @if($product->asal)
                        <span class="product-origin">· Asal: {{ $product->asal }}</span>
                    @endif
                </p>
                <p class="product-price" id="productPrice">
                    Rp{{ number_format((float) ($initialVariant?->harga ?? $product->harga_dasar), 0, ',', '.') }}
                </p>

                @if($product->variants->isNotEmpty())
                <form action="{{ route('cart.add') }}" method="POST" id="buyForm">
                    @csrf
                    <input type="hidden" name="id_varian" id="selectedVariant" value="{{ $initialVariant?->id_varian ?? '' }}">

                    <div class="variant-section">
                        <h4>Warna</h4>
                        <div class="variant-options">
                            @foreach($colors as $color)
                                <button type="button" class="variant-btn {{ $color === $initialColor ? 'selected' : '' }}"
                                    data-value="{{ $color }}" onclick="selectColor(this, this.dataset.value)">{{ $color }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="variant-section">
                        <h4>Ukuran</h4>
                        <div class="variant-options">
                            @foreach($sizes as $size)
                                <button type="button" class="variant-btn {{ $size === $initialSize ? 'selected' : '' }}"
                                    data-value="{{ $size }}" onclick="selectSize(this, this.dataset.value)">{{ $size }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="variant-section">
                        <h4>Jumlah</h4>
                        <div class="qty-selector">
                            <button type="button" onclick="changeQty(-1)">−</button>
                            <input type="number" name="jumlah" id="qtyInput" value="1" min="1" max="{{ $initialVariant?->stok ?? 0 }}" readonly>
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                        <p class="stock-info" id="stockInfo">
                            @if($initialVariant && $initialVariant->stok > 0)
                                Stok tersedia: {{ $initialVariant->stok }}
                            @else
                                Stok habis untuk varian ini.
                            @endif
                        </p>
                    </div>

                    <div class="product-actions">
                        <button type="submit" id="addToCartBtn" class="btn btn-primary" style="flex:1;"
                            {{ ! $initialVariant || $initialVariant->stok < 1 ? 'disabled' : '' }}>Tambah ke Keranjang</button>
                        <button type="submit" formaction="{{ route('checkout.buyNow') }}" id="buyNowBtn" class="btn btn-outline" style="flex:1;"
                            title="Langsung checkout tanpa memasukkan produk ke keranjang"
                            {{ ! $initialVariant || $initialVariant->stok < 1 ? 'disabled' : '' }}>Beli Sekarang</button>
                    </div>
                </form>
                @else
                    <div class="product-actions">
                        <button type="button" class="btn btn-primary" style="flex:1; opacity:.55; cursor:not-allowed;" disabled>Stok Habis</button>
                    </div>
                @endif
            </div>
        </div>

        <!-- DESKRIPSI -->
        <div class="product-description">
            <h3>Deskripsi Produk</h3>
            <p>{{ $product->deskripsi ?: 'Belum ada deskripsi untuk produk ini.' }}</p>
        </div>

        <!-- ULASAN -->
        <div class="reviews-section">
            <div class="reviews-header">
                <h2>Ulasan ({{ $reviews->count() }})</h2>
                @if($avgRating)
                    <div class="reviews-avg">
                        <span class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= round($avgRating) ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span>{{ number_format($avgRating, 1) }} dari 5</span>
                    </div>
                @endif
            </div>

            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="review-card-header">
                        <span class="review-card-user">{{ $review->user->nama }}</span>
                        <span class="review-card-rating">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                    </div>
                    <p class="review-card-text">{{ $review->komentar ?: 'Tidak ada komentar.' }}</p>
                </div>
            @empty
                <div class="no-reviews">Belum ada ulasan untuk produk ini.</div>
            @endforelse
        </div>
    </main>

    @include('layout.partials.footer')

    <script>
        const variants = @json($product->variants);
        const colorImages = @json($colorImages);
        const defaultImage = @json($defaultImageUrl);
        let selectedColor = @json($initialColor);
        let selectedSize = @json($initialSize);

        document.addEventListener('DOMContentLoaded', function() {
            updateStockAndVariant();
        });

        function selectColor(btn, color) {
            selectedColor = color;
            const section = btn.closest('.variant-section');
            section.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            ensureSizeForColor();
            updateStockAndVariant();
        }

        function selectSize(btn, size) {
            selectedSize = size;
            const section = btn.closest('.variant-section');
            section.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            updateStockAndVariant(false);
        }

        function ensureSizeForColor() {
            const currentIsAvailable = variants.some(v =>
                v.warna === selectedColor && v.ukuran === selectedSize && Number(v.stok) > 0
            );

            if (currentIsAvailable) {
                return;
            }

            const fallback = variants.find(v => v.warna === selectedColor && Number(v.stok) > 0)
                || variants.find(v => v.warna === selectedColor);

            if (!fallback) {
                return;
            }

            selectedSize = fallback.ukuran;

            document.querySelectorAll('.variant-section').forEach(function(section) {
                const heading = section.querySelector('h4');
                if (heading && heading.textContent.trim() === 'Ukuran') {
                    section.querySelectorAll('.variant-btn').forEach(function(button) {
                        button.classList.toggle('selected', button.dataset.value === selectedSize);
                    });
                }
            });
        }

        function selectGalleryImage(btn) {
            const color = btn.dataset.color || '';

            if (color) {
                selectedColor = color;

                document.querySelectorAll('.variant-section').forEach(function(section) {
                    const heading = section.querySelector('h4');
                    if (heading && heading.textContent.trim() === 'Warna') {
                        section.querySelectorAll('.variant-btn').forEach(function(button) {
                            button.classList.toggle('selected', button.dataset.value === color);
                        });
                    }
                });

                ensureSizeForColor();

                // Perbarui varian tanpa mengembalikan gambar ke gambar pertama warna.
                updateStockAndVariant(false);
            }

            setMainImage(btn.dataset.src);
        }

        function setMainImage(src) {
            if (!src) return;

            const main = document.getElementById('mainImage');
            if (main) main.src = src;

            document.querySelectorAll('.product-gallery-thumbs .thumb').forEach(function(thumb) {
                thumb.classList.toggle('active', thumb.dataset.src === src);
            });
        }

        function updateColorImage(color) {
            const images = colorImages[color];

            setMainImage(images && images.length ? images[0] : defaultImage);
        }

        function updateStockAndVariant(updateImage = true) {
            if (updateImage) {
                updateColorImage(selectedColor);
            }

            const match = variants.find(v => v.warna === selectedColor && v.ukuran === selectedSize);
            const stockEl = document.getElementById('stockInfo');
            const qtyInput = document.getElementById('qtyInput');
            const addBtn = document.getElementById('addToCartBtn');
            const buyBtn = document.getElementById('buyNowBtn');
            const variantInput = document.getElementById('selectedVariant');

            if (!match) {
                if (variantInput) variantInput.value = '';
                if (stockEl) stockEl.textContent = 'Kombinasi warna dan ukuran ini tidak tersedia.';
                if (addBtn) addBtn.disabled = true;
                if (buyBtn) buyBtn.disabled = true;
                return;
            }

            if (variantInput) variantInput.value = match.id_varian;

            const stok = Number(match.stok) || 0;
            if (stockEl) {
                stockEl.textContent = stok > 0 ? 'Stok tersedia: ' + stok : 'Stok habis untuk varian ini.';
            }
            if (qtyInput) {
                qtyInput.max = stok;
                if (parseInt(qtyInput.value) > stok) {
                    qtyInput.value = Math.max(1, stok);
                }
            }
            if (addBtn) addBtn.disabled = stok < 1;
            if (buyBtn) buyBtn.disabled = stok < 1;

            const priceEl = document.getElementById('productPrice');
            if (priceEl) {
                priceEl.textContent = 'Rp' + Number(match.harga).toLocaleString('id-ID');
            }
        }

        function changeQty(delta) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            const max = parseInt(input.max) || 0;
            if (max > 0 && val > max) val = max;
            input.value = val;
        }
    </script>
</body>
</html>
