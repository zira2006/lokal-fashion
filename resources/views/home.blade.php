<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokal Fashion | Gaya Lokal, Cerita Istimewa</title>
    <meta name="description" content="Temukan fashion lokal Indonesia dengan desain unik, kualitas terbaik, dan cerita di balik setiap karya.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">

    <style>
        .home-page {
            padding: 30px 0 20px;
        }

        .home-shell {
            background: rgba(255, 253, 249, .78);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
        }

        .home-layout {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            gap: 30px;
            align-items: start;
        }

        .home-sidebar {
            position: sticky;
            top: 110px;
        }

        .filter-card {
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
        }

        .filter-section + .filter-section {
            border-top: 1px solid var(--line);
            margin-top: 20px;
            padding-top: 20px;
        }

        .filter-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: var(--dark);
            margin: 0 0 14px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 -6px;
            padding: 6px;
            border-radius: 7px;
            color: var(--text);
            font-size: 13px;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .filter-option:hover {
            background: rgba(233, 220, 203, .5);
        }

        .filter-option.active {
            color: var(--dark);
            font-weight: 600;
        }

        .filter-dot {
            position: relative;
            flex: 0 0 auto;
            width: 14px;
            height: 14px;
            border: 1.5px solid var(--soft-brown);
            border-radius: 50%;
        }

        .filter-option.active .filter-dot {
            border-color: var(--brown);
        }

        .filter-option.active .filter-dot::after {
            content: "";
            position: absolute;
            inset: 2.5px;
            border-radius: 50%;
            background: var(--brown);
        }

        .home-results {
            min-width: 0;
        }

        .home-results-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        .home-results-head h1 {
            margin: 0;
            color: var(--dark);
            font-family: 'Playfair Display', serif;
            font-size: clamp(24px, 2.5vw, 30px);
        }

        .home-results-count {
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .home-product {
            position: relative;
            overflow: hidden;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 14px;
            transition: transform .25s, box-shadow .25s;
        }

        .home-product:hover,
        .home-product:focus-within {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(91, 58, 35, .1);
        }

        .home-product:focus-within {
            outline: 2px solid var(--brown);
            outline-offset: 2px;
        }

        .home-product-link::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .home-product-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 3 / 4;
            background: var(--beige);
        }

        .home-product-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

        .home-product:hover .home-product-image img {
            transform: scale(1.04);
        }

        .home-product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
            padding: 6px 11px;
            border-radius: 20px;
            background: rgba(89, 70, 55, .9);
            color: #fff8ef;
            font-size: 10px;
            font-weight: 700;
            pointer-events: none;
        }

        .home-product-body {
            padding: 15px 16px 17px;
        }

        .home-product-name {
            margin: 0 0 7px;
            color: var(--dark);
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
        }

        .home-product-price {
            margin: 0 0 3px;
            color: var(--brown);
            font-size: 14px;
            font-weight: 700;
        }

        .home-product-store {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
        }

        .home-empty {
            padding: 60px 20px;
            border: 1px dashed var(--line);
            border-radius: 14px;
            background: var(--white);
            color: var(--muted);
            text-align: center;
        }

        .home-empty .btn {
            margin-top: 16px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (max-width: 900px) {
            .home-layout {
                grid-template-columns: 1fr;
            }

            .home-sidebar {
                position: static;
            }

            .home-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .home-page {
                padding-top: 18px;
            }

            .home-shell {
                padding: 14px;
            }

            .filter-card {
                padding: 18px;
            }

            .home-results-head {
                margin-bottom: 14px;
            }

            .home-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container home-page">
        @php
            $activeCategory = is_string(request('kategori')) ? request('kategori') : null;
            $activeCity = is_string(request('kota')) ? request('kota') : null;
            $categoryQuery = request()->except('kategori', 'page');
            $cityQuery = request()->except('kota', 'page');
            $preferredCategories = ['Atasan', 'Bawahan', 'Batik', 'Aksesori'];
            $categories = $categories->sortBy(function ($category) use ($preferredCategories) {
                $position = array_search($category->nama_kategori, $preferredCategories, true);
                return $position === false ? count($preferredCategories) + $category->id_kategori : $position;
            })->values();
        @endphp

        <div class="home-shell">
            <div class="home-layout">
                <aside class="home-sidebar">
                    <div class="filter-card">
                        @include('storefront.partials.sidebar-account-menu', [
                            'categories' => $categories,
                            'activeCategory' => $activeCategory,
                            'categoryQuery' => $categoryQuery,
                            'categoryRoute' => 'home',
                        ])

                        <div class="filter-section">
                            <h2>Kota UMKM</h2>

                            <a href="{{ route('home', $cityQuery) }}"
                               class="filter-option {{ ! $activeCity ? 'active' : '' }}"
                               @if(! $activeCity) aria-current="page" @endif>
                                <span class="filter-dot"></span>
                                Semua kota
                            </a>

                            @foreach ($cities as $city)
                                <a href="{{ route('home', array_merge($cityQuery, ['kota' => $city])) }}"
                                   class="filter-option {{ $activeCity === $city ? 'active' : '' }}"
                                   @if($activeCity === $city) aria-current="page" @endif>
                                    <span class="filter-dot"></span>
                                    {{ $city }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <section class="home-results">
                    <div class="home-results-head">
                        <h1>Produk Lokal</h1>
                        <span class="home-results-count">{{ $products->count() }} produk ditemukan</span>
                    </div>

                    @if ($products->isNotEmpty())
                        <div class="home-grid">
                            @foreach ($products as $product)
                                <article class="home-product">
                                    <div class="home-product-image">
                                        <img src="{{ $product->gambarUrl }}" alt="{{ $product->nama_produk }}" loading="lazy">

                                        @if ($product->totalStok() < 1)
                                            <span class="home-product-badge">Stok Habis</span>
                                        @endif
                                    </div>

                                    <div class="home-product-body">
                                        <a href="{{ route('product.detail', Str::slug($product->nama_produk)) }}" class="home-product-link">
                                            <h2 class="home-product-name">{{ $product->nama_produk }}</h2>
                                        </a>
                                        <p class="home-product-price">Rp{{ number_format($product->harga_dasar, 0, ',', '.') }}</p>
                                        <p class="home-product-store">{{ $product->store?->nama_toko ?? 'UMKM Lokal' }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="home-empty">
                            <p>Belum ada produk yang cocok dengan filter pilihanmu.</p>
                            <a href="{{ route('home') }}" class="btn btn-outline btn-sm">Reset Filter</a>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
