<aside class="account-sidebar">
    <h3>Menu</h3>
    <nav class="account-menu">
        <a href="{{ route('profile.index') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}">Profil Saya</a>
        <a href="{{ route('orders.index') }}" class="{{ ($active ?? '') === 'orders' ? 'active' : '' }}">Pesanan Saya</a>
        <a href="{{ route('profile.addresses') }}" class="{{ ($active ?? '') === 'addresses' ? 'active' : '' }}">Alamat Pengiriman</a>
        <a href="{{ route('profile.index') }}#ganti-password">Ganti Password</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </nav>
</aside>
