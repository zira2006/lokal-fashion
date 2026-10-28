
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .catalog-page {
            padding: 30px 0 60px;
        }

        .catalog-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 35px;
            align-items: start;
        }

        .catalog-sidebar {
            position: sticky;
            top: 100px;
        }

        .filter-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 20px;
        }

        .filter-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: var(--dark);
            margin: 0 0 16px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-group h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 10px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 5px 0;
            font-size: 13px;
            color: var(--text);
            cursor: pointer;
        }

        .filter-option input[type="checkbox"],
        .filter-option input[type="radio"] {
            accent-color: var(--brown);
            width: 15px;
            height: 15px;
        }

        .filter-option:hover {
            color: var(--dark);
        }

        /* Kategori berupa tautan agar sekali klik langsung memfilter */
        a.filter-option {
            text-decoration: none;
            border-radius: 7px;
            padding: 5px 6px;
            margin: 0 -6px;
            transition: background 0.2s, color 0.2s;
        }

        a.filter-option:hover {
            background: var(--cream);
        }

        a.filter-option.active {
            color: var(--dark);
            font-weight: 600;
        }

        .filter-dot {
            width: 15px;
            height: 15px;
            border: 1.5px solid var(--soft-brown);
            border-radius: 50%;
            flex-shrink: 0;
            position: relative;
            background: transparent;
        }

        a.filter-option.active .filter-dot {
            border-color: var(--brown);
        }

        a.filter-option.active .filter-dot::after {
            content: "";
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: var(--brown);
        }

        .catalog-results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .catalog-results-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--dark);
            margin: 0;
        }

        .results-count {
            font-size: 13px;
            color: var(--muted);
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .product-card {
            position: relative;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.25s, box-shadow 0.25s;
            text-decoration: none;
            display: block;
        }

        /* Seluruh kartu produk bisa diklik lewat tautan nama produk */
        .product-card-link::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .product-card-buy-link {
            position: relative;
            z-index: 2;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(91, 58, 35, 0.10);
        }

        .product-card-img {
            display: block;
            position: relative;
            width: 100%;
            aspect-ratio: 3 / 4;
            background: var(--beige);
            overflow: hidden;
        }

        .product-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s;
        }

        .product-card:hover .product-card-img img {
            transform: scale(1.05);
        }

        .product-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(89, 70, 55, 0.9);
            color: #fff8ef;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .product-card-body {
            padding: 16px;
        }

        .product-card-category {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .product-card-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            margin: 0 0 6px;
        }

        .product-card-name:hover {
            color: var(--brown);
        }

        .product-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .product-card-price {
            font-size: 14px;
            font-weight: 700;
            color: var(--brown);
        }

        .product-card-buy-link,
        .product-card-buy-disabled {
            display: inline-block;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            background: var(--dark);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .product-card-buy-link:hover {
            background: #6d5340;
        }

        .product-card-buy-disabled {
            background: var(--line);
            color: var(--muted);
            cursor: not-allowed;
        }

        .product-card-store {
            font-size: 11px;
            color: var(--muted);
            margin-top: 8px;
        }

        .product-card-origin {
            color: var(--brown);
        }

        .catalog-empty {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border: 1px dashed var(--line);
            border-radius: 14px;
            color: var(--muted);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
        }

        .pagination a,
        .pagination span {
            padding: 9px 15px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 13px;
            color: var(--text);
            text-decoration: none;
            background: var(--white);
        }

        .pagination .active {
            background: var(--dark);
            color: #fff;
            border-color: var(--dark);
        }

        @media (max-width: 900px) {
            .catalog-layout {
                grid-template-columns: 1fr;
            }
            .catalog-sidebar {
                position: static;
            }
            .catalog-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 520px) {
            .catalog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    @include('layout.partials.header')

    <main class="container catalog-page">
        <div class="catalog-layout">
            <!-- SIDEBAR MENU & FILTER -->
            <aside class="catalog-sidebar">
                @php
                    $categoryQuery = request()->except('kategori', 'page');
                    $cityQuery = request()->except('kota', 'page');
                    $activeCategory = is_string(request('kategori')) ? request('kategori') : null;
                    $activeCity = is_string(request('kota')) ? request('kota') : null;
                @endphp
                <div class="filter-card">
                    @include('storefront.partials.sidebar-account-menu', [
                        'categories' => $categories,
                        'activeCategory' => $activeCategory,
                        'categoryQuery' => $categoryQuery,
                        'categoryRoute' => 'catalog',
                    ])

                    <div class="filter-group">
                        <h4>Kota UMKM</h4>

                        <a href="{{ route('catalog', $cityQuery) }}"
                           class="filter-option {{ ! $activeCity ? 'active' : '' }}"
                           @if(! $activeCity) aria-current="page" @endif>
                            <span class="filter-dot"></span> Semua
                        </a>

                        @foreach($cities as $city)
                            <a href="{{ route('catalog', array_merge($cityQuery, ['kota' => $city])) }}"
                               class="filter-option {{ $activeCity === $city ? 'active' : '' }}"
                               @if($activeCity === $city) aria-current="page" @endif>
                                <span class="filter-dot"></span> {{ $city }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- HASIL -->
            <div>
                @php
                    $catalogTotal = $products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
                        ? $products->total()
                        : $products->count();
                @endphp
                <div class="catalog-results-header">
                    <h1>Katalog Produk</h1>
                    <span class="results-count">{{ $catalogTotal }} produk ditemukan</span>
                </div>

                @if($products->isNotEmpty())
                    <div class="catalog-grid">
                        @foreach($products as $product)
                            <div class="product-card">
                                <div class="product-card-img">
                                    <img src="{{ $product->gambar_url }}" alt="{{ $product->nama_produk }}" loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                                    @if($product->totalStok() < 1)
                                        <span class="product-card-badge">Stok Habis</span>
                                    @endif
                                </div>
                                <div class="product-card-body">
                                    <p class="product-card-category">{{ $product->category->nama_kategori ?? 'Fashion Lokal' }}</p>
                                    <a href="{{ route('product.detail', Str::slug($product->nama_produk)) }}" class="product-card-link">
                                        <h3 class="product-card-name">{{ $product->nama_produk }}</h3>
                                    </a>
                                    <div class="product-card-bottom">
                                        <p class="product-card-price">Rp{{ number_format((float) $product->harga_dasar, 0, ',', '.') }}</p>
                                        @if($varian = $product->varian_tersedia)
                                            <a href="{{ route('product.detail', Str::slug($product->nama_produk)) }}"
                                               class="product-card-buy-link"
                                               title="Pilih warna dan ukuran lalu beli langsung">
                                                Pilih Varian
                                            </a>
                                        @else
                                            <span class="product-card-buy-disabled">Stok Habis</span>
                                        @endif
                                    </div>
                                    <p class="product-card-store">
                                        {{ $product->store?->nama_toko ?? '' }}
                                        @if($product->asal)
                                            <span class="product-card-origin">· Asal: {{ $product->asal }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $products->hasPages())
                        <div class="pagination">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="catalog-empty">
                        <p>Tidak ada produk yang cocok dengan filter.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
