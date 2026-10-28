@php
    $sidebarUser = Auth::user();
    $isAdmin = $sidebarUser?->isAdmin() ?? false;
    $roleLabel = $isAdmin
        ? 'Admin'
        : (isset($store) && $store ? $store->nama_toko : 'Penjual');
    $initial = mb_strtoupper(mb_substr($sidebarUser?->nama ?? 'L', 0, 1));
@endphp

<aside class="app-sidebar">
    <div class="sidebar-brand-row">
        @include('layout.partials.back-button', ['compact' => true, 'variant' => 'sidebar'])
        <a href="{{ route('dashboard') }}" class="brand">Lokal Fashion</a>
    </div>

    <div class="app-sidebar-user">
        <span class="app-sidebar-avatar">{{ $initial }}</span>
        <div class="app-sidebar-user-text">
            <strong>{{ $sidebarUser?->nama ?? 'Pengguna' }}</strong>
            <span>{{ $roleLabel }}</span>
        </div>
    </div>

    <nav class="app-sidebar-nav" aria-label="Menu dashboard">
        <span class="app-sidebar-label">{{ $isAdmin ? 'Administrasi' : 'Pusat Penjual' }}</span>

        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="app-nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        @if($isAdmin)
            <a href="{{ route('admin.users') }}"
               class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3.5 20c.6-3.2 2.8-5.2 5.5-5.2s4.9 2 5.5 5.2"></path>
                        <path d="M16 5.5a3 3 0 0 1 0 5.8"></path>
                        <path d="M17.5 15.2c1.7.6 2.8 2.2 3 4.8"></path>
                    </svg>
                </span>
                <span>Pengguna</span>
            </a>
            <a href="{{ route('admin.sellers') }}"
               class="{{ request()->routeIs('admin.sellers*') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10v10h16V10"></path>
                        <path d="M3 10l2-6h14l2 6"></path>
                        <path d="M3 10a3 3 0 0 0 5 2 3 3 0 0 0 4 0 3 3 0 0 0 4 0 3 3 0 0 0 5-2"></path>
                    </svg>
                </span>
                <span>Toko &amp; Penjual</span>
            </a>
            <a href="{{ route('admin.orders') }}"
               class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path>
                        <path d="M9 8h6M9 12h6"></path>
                    </svg>
                </span>
                <span>Semua Pesanan</span>
            </a>
        @else
            <a href="{{ route('seller.products') }}"
               class="{{ request()->routeIs('seller.products') || request()->routeIs('seller.products.edit') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 13 13 20a2 2 0 0 1-2.8 0L3 12.8V3h9.8L20 10.2a2 2 0 0 1 0 2.8Z"></path>
                        <circle cx="8" cy="8" r="1.2"></circle>
                    </svg>
                </span>
                <span>Produk Saya</span>
            </a>
            <a href="{{ route('seller.products.create') }}"
               class="{{ request()->routeIs('seller.products.create') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="8.5"></circle>
                        <path d="M12 8v8M8 12h8"></path>
                    </svg>
                </span>
                <span>Tambah Produk</span>
            </a>
            <a href="{{ route('seller.orders') }}"
               class="{{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path>
                        <path d="M9 8h6M9 12h6"></path>
                    </svg>
                </span>
                <span>Pesanan</span>
            </a>
            <a href="{{ route('seller.profile') }}"
               class="{{ request()->routeIs('seller.profile*') ? 'active' : '' }}">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10v10h16V10"></path>
                        <path d="M3 10l2-6h14l2 6"></path>
                        <path d="M3 10a3 3 0 0 0 5 2 3 3 0 0 0 4 0 3 3 0 0 0 4 0 3 3 0 0 0 5-2"></path>
                    </svg>
                </span>
                <span>Profil Toko</span>
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="app-sidebar-logout">
            @csrf
            <button type="submit">
                <span class="app-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 5H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h4"></path>
                        <path d="M14 8l4 4-4 4"></path>
                        <path d="M18 12H9"></path>
                    </svg>
                </span>
                <span>Keluar</span>
            </button>
        </form>
    </nav>
</aside>
