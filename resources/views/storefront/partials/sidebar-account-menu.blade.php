<div class="sidebar-account-menu">
    <h3>Menu</h3>

    @auth
        <nav class="account-menu sidebar-menu-links" aria-label="Menu akun">
            <a href="{{ route('profile.index') }}"
               class="{{ request()->routeIs('profile.index') ? 'active' : '' }}">
                <span class="sidebar-menu-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5.5 20c.8-3.4 3.4-5.3 6.5-5.3s5.7 1.9 6.5 5.3"></path>
                    </svg>
                </span>
                <span>Profil Saya</span>
            </a>
            <a href="{{ route('orders.index') }}"
               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 8h12l-1 12H7L6 8Z"></path>
                        <path d="M9 8V6a3 3 0 0 1 6 0v2"></path>
                    </svg>
                </span>
                <span>Pesanan Saya</span>
            </a>
            <a href="{{ route('profile.addresses') }}"
               class="{{ request()->routeIs('profile.addresses*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg>
                </span>
                <span>Alamat Pengiriman</span>
            </a>
            <a href="{{ route('profile.index') }}#ganti-password">
                <span class="sidebar-menu-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path d="M8 10V8a4 4 0 0 1 8 0v2"></path>
                    </svg>
                </span>
                <span>Ganti Password</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <span class="sidebar-menu-icon" aria-hidden="true">
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
    @endauth

    @isset($categories)
        <details class="sidebar-category-menu" id="kategori-menu">
            <summary>
                <span class="sidebar-menu-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="4" width="6" height="6" rx="1.5"></rect>
                        <rect x="14" y="4" width="6" height="6" rx="1.5"></rect>
                        <rect x="4" y="14" width="6" height="6" rx="1.5"></rect>
                        <rect x="14" y="14" width="6" height="6" rx="1.5"></rect>
                    </svg>
                </span>
                <span>Kategori</span>
            </summary>
            <nav class="account-menu category-menu" aria-label="Menu kategori">
                <a href="{{ route($categoryRoute, $categoryQuery) }}"
                   class="{{ ! $activeCategory ? 'active' : '' }}"
                   @if(! $activeCategory) aria-current="page" @endif>
                    Semua
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route($categoryRoute, array_merge($categoryQuery, ['kategori' => $category->id_kategori])) }}"
                       class="{{ (string) $activeCategory === (string) $category->id_kategori ? 'active' : '' }}"
                       @if((string) $activeCategory === (string) $category->id_kategori) aria-current="page" @endif>
                        {{ $category->nama_kategori }}
                    </a>
                @endforeach
            </nav>
        </details>
    @endisset
</div>
