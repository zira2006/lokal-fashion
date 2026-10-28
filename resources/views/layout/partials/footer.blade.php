<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <div class="footer-brand">Lokal Fashion</div>
            <p>Menghadirkan karya fashion lokal Indonesia dengan cerita istimewa.</p>
        </div>
        <div>
            <h4>Jelajahi</h4>
            <a href="{{ route('catalog') }}">Katalog</a>
            <a href="{{ route('home') }}">Beranda</a>
        </div>
        <div>
            <h4>Akun</h4>
            @auth
                <a href="{{ route('profile.index') }}">Profil Saya</a>
                <a href="{{ route('profile.addresses') }}">Alamat</a>
                <a href="{{ route('orders.index') }}">Pesanan Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="footer-link-btn">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}">Masuk / Daftar</a>
            @endauth
        </div>
    </div>
    <div class="container footer-copy">
        &copy; {{ date('Y') }} Lokal Fashion &mdash; Karya Lokal Indonesia
    </div>
</footer>

@include('layout.partials.buyer-bottom-nav')
