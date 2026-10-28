<div class="browser-bar">
    <div class="browser-dots">
        <span class="dot dot-red"></span>
        <span class="dot dot-yellow"></span>
        <span class="dot dot-green"></span>
    </div>
    <div class="browser-url">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        <span>lokal-fashion.id{{ $_SERVER['REQUEST_URI'] ?? '/' }}</span>
    </div>
    <div class="browser-bookmark">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
    </div>
</div>

<header class="site-header">
    <div class="container header-inner">
        <div class="header-brand-group">
            @include('layout.partials.back-button', ['compact' => true, 'variant' => 'brand'])
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-name">Lokal Fashion</span>
            </a>
        </div>

        <form action="{{ request()->routeIs('home') ? route('home') : route('catalog') }}" method="get" class="header-search">
            <input type="search" name="q" value="{{ is_string(request('q')) ? request('q') : '' }}" placeholder="Cari baju, batik, aksesoris..." aria-label="Cari produk">
            @if(request()->filled('q'))
                <button type="button" class="search-clear" onclick="clearHeaderSearch(this)"
                        aria-label="Hapus pencarian dan tampilkan semua produk" title="Tampilkan semua produk">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </form>

        <nav class="header-nav">
            <a href="{{ route('catalog') }}">Katalog</a>
            <a href="{{ route('cart.index') }}" class="nav-cart" aria-label="Keranjang" title="Keranjang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="9" cy="20" r="1.4"></circle>
                    <circle cx="18" cy="20" r="1.4"></circle>
                    <path d="M3 3h2l2.4 11.2a1.5 1.5 0 0 0 1.5 1.2h8.9a1.5 1.5 0 0 0 1.5-1.2L21 7H6"></path>
                </svg>
                @php
                    $cartCount = collect(session('cart', []))->sum('jumlah');
                @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>
            @auth
                @if(Auth::user()->isSeller())
                    <a href="{{ route('dashboard') }}">Pusat Penjual</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}">Dashboard Admin</a>
                @endif
                <div class="nav-dropdown">
                    <button class="nav-dropdown-btn nav-dots-btn" type="button" onclick="toggleAccountMenu(this)"
                            aria-label="Menu akun" aria-haspopup="true" aria-expanded="false" title="Menu akun">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <circle cx="12" cy="5" r="1.8"></circle>
                            <circle cx="12" cy="12" r="1.8"></circle>
                            <circle cx="12" cy="19" r="1.8"></circle>
                        </svg>
                    </button>
                    <div class="nav-dropdown-menu">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('dashboard') }}">Dashboard Admin</a>
                        @else
                            <a href="{{ route('profile.index') }}">Profil Saya</a>
                            <a href="{{ route('orders.index') }}">Pesanan Saya</a>
                            <a href="{{ route('profile.addresses') }}">Alamat Pengiriman</a>
                            <a href="{{ route('profile.index') }}#ganti-password">Ganti Password</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
            @endauth
        </nav>
    </div>
</header>

@if(session('success') || session('error') || session('info'))
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
    </div>
@endif

<script>
    function clearHeaderSearch(button) {
        const form = button.closest('.header-search');
        if (!form) return;

        const input = form.querySelector('input[type="search"]');
        if (input) input.value = '';

        window.location.href = form.action;
    }

    function toggleAccountMenu(button) {
        const dropdown = button.parentElement;
        const isOpen = dropdown.classList.toggle('open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.header-search input[type="search"]').forEach(function(input) {
            // Tombol clear bawaan browser: kosongkan dan tampilkan semua produk.
            input.addEventListener('search', function() {
                if (this.value.trim() === '') {
                    window.location.href = this.form.action;
                }
            });

            // Tekan Escape untuk menutup pencarian dan kembali ke semua produk.
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && this.value.trim() !== '') {
                    this.value = '';
                    window.location.href = this.form.action;
                }
            });
        });

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.nav-dropdown.open').forEach(dd => {
                if (!dd.contains(e.target)) {
                    dd.classList.remove('open');
                    const button = dd.querySelector('.nav-dropdown-btn');
                    if (button) button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
</script>

<style>
    .browser-bar {
        background: #4a3728;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .browser-dots { display: flex; gap: 7px; }
    .dot { width: 11px; height: 11px; border-radius: 50%; }
    .dot-red { background: #e06c5a; }
    .dot-yellow { background: #e8c85a; }
    .dot-green { background: #6fbf5a; }
    .browser-url {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(0,0,0,.25);
        border-radius: 20px;
        padding: 6px 16px;
        color: #d4c3b2;
        font-size: 12px;
    }
    .browser-bookmark { color: #d4c3b2; opacity: .6; }

    .site-header {
        background: var(--white);
        border-bottom: 1px solid var(--line);
        padding: 18px 0;
    }
    .header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
    }
    .brand-name {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--dark);
    }
    .header-search { position: relative; }
    .header-search input {
        width: 300px;
        padding: 10px 40px 10px 16px;
        border: 1px solid var(--line);
        border-radius: 30px;
        background: var(--white);
        outline: none;
        font-size: 13px;
    }
    .header-search input:focus { border-color: var(--brown); }
    .header-search input[type="search"]::-webkit-search-cancel-button,
    .header-search input[type="search"]::-ms-clear { display: none; }
    .search-clear {
        position: absolute;
        top: 50%;
        right: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        padding: 0;
        border: none;
        border-radius: 50%;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        transform: translateY(-50%);
        transition: background .2s, color .2s;
    }
    .search-clear:hover { background: var(--cream); color: var(--brown); }
    .search-clear:focus-visible { outline: 2px solid var(--brown); outline-offset: 2px; }
    .header-nav {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 14px;
    }
    .header-nav > a { color: var(--text); font-weight: 500; }
    .header-nav > a:hover { color: var(--brown); }
    .nav-cart {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: var(--dark);
        transition: background .2s, color .2s;
    }
    .nav-cart:hover { background: var(--cream); color: var(--brown); }
    .nav-cart svg {
        width: 20px;
        height: 20px;
        transition: transform .2s;
    }
    .nav-cart:hover svg { transform: translateY(-1px); }
    .cart-badge {
        position: absolute;
        top: -2px;
        right: -3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        border: 2px solid var(--white);
        border-radius: 999px;
        background: var(--brown);
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        line-height: 1;
    }
    .nav-dropdown { position: relative; }
    .nav-dropdown-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: none;
        border: none;
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
        cursor: pointer;
        padding: 6px 0;
    }
    .nav-dots-btn {
        width: 34px;
        height: 34px;
        justify-content: center;
        padding: 0;
        border-radius: 50%;
        color: var(--dark);
        transition: background .2s, color .2s;
    }
    .nav-dots-btn:hover {
        background: var(--cream);
        color: var(--brown);
    }
    .nav-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        min-width: 160px;
        background: var(--white);
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(91,58,35,.12);
        padding: 8px 0;
        z-index: 50;
    }
    .nav-dropdown.open .nav-dropdown-menu { display: block; }
    .nav-dropdown-menu.open { display: block; }
    .nav-dropdown-menu a, .nav-dropdown-menu button {
        display: block;
        width: 100%;
        padding: 10px 18px;
        font-size: 13px;
        color: var(--text);
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
    }
    .nav-dropdown-menu a:hover, .nav-dropdown-menu button:hover { background: var(--cream); }
    .btn { display: inline-block; padding: 10px 20px; border: none; border-radius: 25px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .btn-primary { background: var(--dark); color: #fff; }
    .btn-primary:hover { background: #6d5340; }
    .header-nav > a.btn-primary { color: #fff; }
    .header-nav > a.btn-primary:hover { color: #fff; }
    .btn-sm { padding: 8px 16px; font-size: 12px; }
</style>
