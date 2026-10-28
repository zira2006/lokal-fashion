@foreach($items as $item)
    <div class="order-item">
        <div>
            <div class="order-item-name">{{ $item['variant']->product->nama_produk }}</div>
            <div class="order-item-variant">{{ $item['variant']->warna }}, {{ $item['variant']->ukuran }} x {{ $item['jumlah'] }}</div>
            @if($item['variant']->product->asal)
                <div class="order-item-origin">Asal: {{ $item['variant']->product->asal }}</div>
            @endif
        </div>
        <span>Rp{{ number_format((float) $item['subtotal'], 0, ',', '.') }}</span>
    </div>
@endforeach

<div class="order-summary-row total">
    <span>Total</span>
    <span>Rp{{ number_format((float) $total, 0, ',', '.') }}</span>
</div>

<div style="margin-top: 12px; text-align: right;">
    @if($isDirectCheckout ?? false)
        <button type="submit" form="cancelBuyNowForm" style="background: none; border: none; padding: 0; font: inherit; font-size: 12px; color: var(--brown); text-decoration: underline; cursor: pointer;">
            Batalkan beli langsung
        </button>
    @else
        <a href="{{ route('cart.index') }}" style="font-size: 12px; color: var(--brown); text-decoration: underline;">Ubah pilihan produk</a>
    @endif
</div>
