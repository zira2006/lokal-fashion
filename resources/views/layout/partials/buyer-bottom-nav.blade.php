@php
    $profileUrl = auth()->check() ? route('profile.index') : route('login');
    $isHomeActive = request()->routeIs('home');
    $isProfileActive = auth()->check() && (request()->routeIs('profile.*') || request()->routeIs('orders.*'));
@endphp

<nav class="buyer-bottom-nav" aria-label="Navigasi pembeli">
    <a href="{{ route('home') }}"
       class="buyer-bottom-link {{ $isHomeActive ? 'active' : '' }}"
       @if($isHomeActive) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 10.5 12 3l9 7.5"></path>
            <path d="M5 9.5V21h14V9.5"></path>
            <path d="M9 21v-7h6v7"></path>
        </svg>
        <span>Beranda</span>
    </a>

    <a href="{{ $profileUrl }}"
       class="buyer-bottom-link {{ $isProfileActive ? 'active' : '' }}"
       @if($isProfileActive) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="8" r="3.5"></circle>
            <path d="M5 21c.8-3.5 3.4-5.5 7-5.5s6.2 2 7 5.5"></path>
        </svg>
        <span>Profil</span>
    </a>
</nav>

<style>
    body {
        padding-bottom: 96px;
    }
</style>
