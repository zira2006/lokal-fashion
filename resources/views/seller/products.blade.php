
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya | Pusat Penjual - Lokal Fashion</title>
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
        .seller-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .seller-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0; }
        .seller-header p { font-size: 13px; color: var(--muted); margin: 5px 0 0; }

        .btn { display: inline-block; padding: 12px 24px; border: none; border-radius: 25px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-danger { background: #a8442e; color: #fff; }
        .btn-danger:hover { background: #8e3624; }
        .btn-sm { padding: 8px 16px; font-size: 12px; }

        .flash { padding: 13px 18px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .flash-success { background: #e5f2e5; border: 1px solid #b9d8b9; color: #3d6b3e; }
        .flash-error { background: #f8e7e3; border: 1px solid #e7c0b7; color: #8e3624; }

        .seller-table-wrap { overflow-x: auto; }
        .seller-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: 14px; overflow: hidden; border: 1px solid var(--line); }
        .seller-table th { background: var(--beige); padding: 14px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .seller-table td { padding: 14px 16px; border-top: 1px solid var(--line); font-size: 13px; vertical-align: middle; }
        .seller-table tr:hover td { background: #fdf9f4; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-aktif { background: #d9edda; color: #3d6b3e; }
        .status-nonaktif { background: #f0e8de; color: #8b715d; }

        .seller-empty { text-align: center; padding: 60px 20px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; color: var(--muted); }

        .greeting { font-size: 13px; color: var(--muted); margin-bottom: 25px; }
        .greeting strong { color: var(--dark); }

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
            <p class="greeting">Halo, <strong>{{ Auth::user()->nama }}</strong> ({{ $store->nama_toko }}) &mdash; <span style="color: #6a8f5a;">{{ $store->status_verifikasi }}</span></p>

            <div class="seller-header">
                <div>
                    <h1>Produk Saya</h1>
                    <p>Toko: {{ $store->nama_toko }} &nbsp;|&nbsp; {{ $store->status_verifikasi }}</p>
                </div>
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
            </div>

            @if(session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            @if($products->isNotEmpty())
                <div class="seller-table-wrap">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <img src="{{ $product->gambar_url }}" alt="{{ $product->nama_produk }}" style="width:45px; height:45px; border-radius:8px; object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                                            <div>
                                                <strong style="color:var(--dark);">{{ $product->nama_produk }}</strong>
                                                <div style="font-size:11px; color:var(--muted);">Asal: {{ $product->asal ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->nama_kategori ?? '-' }}</td>
                                    <td>{{ $product->totalStok() }}</td>
                                    <td><span class="status-badge status-{{ $product->status }}">{{ ucfirst($product->status) }}</span></td>
                                    <td>
                                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <a href="{{ route('seller.products.edit', $product->id_produk) }}" class="btn btn-outline btn-sm">Edit</a>
                                            <form method="POST" action="{{ route('seller.products.toggle', $product->id_produk) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $product->status === 'aktif' ? 'btn-outline' : 'btn-primary' }}">
                                                    {{ $product->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('seller.products.destroy', $product->id_produk) }}"
                                                  onsubmit="return confirm('Hapus produk ini? Tindakan ini tidak bisa dibatalkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="seller-empty">
                    <p>Belum ada produk. Tambahkan produk pertamamu!</p>
                </div>
            @endif
        </main>
    </div>
</body>
</html>
