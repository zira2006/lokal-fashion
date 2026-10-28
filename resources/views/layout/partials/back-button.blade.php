@php
    $routeName = request()->route()?->getName() ?? '';
    $invoice = request()->route('invoice');
    $compact = $compact ?? false;
    $variant = $variant ?? null;

    $fallback = match (true) {
        $routeName === 'catalog' => route('home'),
        $routeName === 'product.detail' => route('catalog'),
        $routeName === 'cart.index' => route('catalog'),
        $routeName === 'checkout' => route('cart.index'),
        in_array($routeName, ['payment.show', 'payment.pay'], true) => route('orders.index'),
        $routeName === 'payment.connect.form' && $invoice => route('payment.show', $invoice),
        $routeName === 'orders.show' => route('orders.index'),
        $routeName === 'orders.index' => route('home'),
        $routeName === 'profile.addresses' => route('profile.index'),
        $routeName === 'profile.addresses.edit' => route('profile.addresses'),
        $routeName === 'profile.index' => route('home'),
        $routeName === 'seller.profile.edit' => route('seller.profile'),
        in_array($routeName, ['seller.products.create', 'seller.products.edit'], true) => route('seller.products'),
        str_starts_with($routeName, 'seller.') => route('dashboard'),
        str_starts_with($routeName, 'admin.') => route('dashboard'),
        $routeName === 'dashboard' => route('home'),
        default => route('home'),
    };

    $buttonClass = 'back-button';

    if ($compact) {
        $buttonClass .= ' back-button--icon';
    }

    if ($variant) {
        $buttonClass .= ' back-button--'.$variant;
    }
@endphp

<a href="{{ $fallback }}" class="{{ $buttonClass }}" onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }" aria-label="Kembali" title="Kembali">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
    @unless($compact)
        <span>Kembali</span>
    @endunless
</a>
