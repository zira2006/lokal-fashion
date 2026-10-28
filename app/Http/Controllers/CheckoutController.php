<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Support\CartStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $directRow = $this->directCheckoutRow();
        $isDirectCheckout = $directRow !== null;
        $rawCart = session()->get('cart', []);
        $cart = $isDirectCheckout ? [$directRow] : $this->applySelection($rawCart);

        if (empty($cart)) {
            if ($isDirectCheckout) {
                session()->forget('direct_checkout');

                return redirect()->route('cart.index')
                    ->with('error', 'Produk yang ingin dibeli langsung sudah tidak tersedia.');
            }

            if (empty($rawCart)) {
                return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
            }

            session()->forget('checkout_selection');

            return redirect()->route('cart.index')
                ->with('error', 'Produk yang dipilih tidak tersedia di keranjang.');
        }

        $variantIds = array_column($cart, 'id_varian');
        $variants = Variant::with(['product.store', 'product.category'])
            ->whereIn('id_varian', $variantIds)
            ->whereHas('product', fn ($query) => $query->where('status', 'aktif')->dariPenjual())
            ->get()
            ->keyBy('id_varian');

        $items = [];
        $total = 0;

        foreach ($cart as $row) {
            $variant = $variants->get($row['id_varian']);
            if (! $variant) {
                continue;
            }

            if ($variant->stok < $row['jumlah']) {
                if ($isDirectCheckout) {
                    session()->forget('direct_checkout');
                }

                return redirect()->route('cart.index')
                    ->with('error', "Stok {$variant->product->nama_produk} tidak mencukupi.");
            }

            $subtotal = $variant->harga * $row['jumlah'];
            $total += $subtotal;

            $items[] = [
                'variant' => $variant,
                'jumlah' => $row['jumlah'],
                'subtotal' => $subtotal,
            ];
        }

        if (empty($items)) {
            if ($isDirectCheckout) {
                session()->forget('direct_checkout');

                return redirect()->route('cart.index')
                    ->with('error', 'Produk yang ingin dibeli langsung sudah tidak tersedia.');
            }

            session()->forget('checkout_selection');

            return redirect()->route('cart.index')
                ->with('error', 'Produk di keranjang sudah tidak tersedia.');
        }

        $addresses = Address::where('id_user', Auth::id())->get();

        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        return view('storefront.checkout', compact(
            'items',
            'total',
            'addresses',
            'defaultAddress',
            'isDirectCheckout'
        ));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
        ]);

        if (Address::where('id_user', Auth::id())->exists()) {
            return back()->with('error', 'Hapus alamat lama terlebih dahulu sebelum menambahkan alamat baru.');
        }

        $validated['id_user'] = Auth::id();
        $validated['is_default'] = Address::where('id_user', Auth::id())->count() === 0;

        Address::create($validated);

        return redirect()->route('checkout')
            ->with('success', 'Alamat berhasil disimpan. Silakan periksa pesanan dan buat pesanan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $directRow = $this->directCheckoutRow();
        $isDirectCheckout = $directRow !== null;
        $rawCart = session()->get('cart', []);
        $cart = $isDirectCheckout ? [$directRow] : $this->applySelection($rawCart);

        if (empty($cart)) {
            if ($isDirectCheckout) {
                session()->forget('direct_checkout');

                return redirect()->route('cart.index')
                    ->with('error', 'Produk yang ingin dibeli langsung sudah tidak tersedia.');
            }

            if (empty($rawCart)) {
                return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
            }

            session()->forget('checkout_selection');

            return redirect()->route('cart.index')
                ->with('error', 'Produk yang dipilih tidak tersedia di keranjang.');
        }

        $validated = $request->validate([
            'id_alamat' => ['required', 'exists:addresses,id_alamat'],
        ]);

        $address = Address::where('id_alamat', $validated['id_alamat'])
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $variantIds = array_column($cart, 'id_varian');
        $variants = Variant::whereIn('id_varian', $variantIds)
            ->whereHas('product', fn ($query) => $query->where('status', 'aktif')->dariPenjual())
            ->get()
            ->keyBy('id_varian');

        $total = 0;
        foreach ($cart as $row) {
            $variant = $variants->get($row['id_varian']);
            if (! $variant || $variant->stok < $row['jumlah']) {
                if ($isDirectCheckout) {
                    session()->forget('direct_checkout');
                }

                return redirect()->route('cart.index')
                    ->with('error', 'Stok produk tidak mencukupi.');
            }
            $total += $variant->harga * $row['jumlah'];
        }

        $invoice = 'INV-'.date('Y').'-'.str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'no_invoice' => $invoice,
                'id_user' => Auth::id(),
                'id_alamat' => $address->id_alamat,
                'status' => 'baru',
                'total_harga' => $total,
            ]);

            foreach ($cart as $row) {
                $variant = $variants->get($row['id_varian']);

                OrderItem::create([
                    'id_pesanan' => $order->id_pesanan,
                    'id_varian' => $variant->id_varian,
                    'jumlah' => $row['jumlah'],
                    'harga' => $variant->harga,
                ]);

                $variant->decrement('stok', $row['jumlah']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }

        if ($isDirectCheckout) {
            // Beli langsung tidak boleh mengubah isi keranjang.
            session()->forget('direct_checkout');
        } else {
            $purchasedIds = array_map(static fn ($row) => (int) $row['id_varian'], $cart);
            $remaining = array_values(array_filter(
                session()->get('cart', []),
                static fn ($row) => ! in_array((int) $row['id_varian'], $purchasedIds, true)
            ));

            if (empty($remaining)) {
                session()->forget('cart');
                CartStorage::clearForUser((int) Auth::id());
            } else {
                $remaining = CartStorage::replaceForUser((int) Auth::id(), $remaining);
                session()->put('cart', $remaining);
            }

            session()->put('cart_loaded_from_db', true);
        }

        session()->forget('checkout_selection');

        return redirect()->route('payment.show', $order->no_invoice)
            ->with('success', "Pesanan {$invoice} berhasil dibuat.");
    }

    public function buyNow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_varian' => ['required', 'exists:varian,id_varian'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $variant = Variant::with('product')
            ->whereHas('product', fn ($query) => $query->where('status', 'aktif')->dariPenjual())
            ->findOrFail($validated['id_varian']);

        if ($variant->stok < $validated['jumlah']) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        session()->forget('checkout_selection');
        session()->put('direct_checkout', [
            'id_varian' => (int) $variant->id_varian,
            'jumlah' => (int) $validated['jumlah'],
            'product_slug' => Str::slug($variant->product->nama_produk),
        ]);

        return redirect()->route('checkout');
    }

    public function cancelBuyNow(): RedirectResponse
    {
        $direct = session()->get('direct_checkout');
        session()->forget('direct_checkout');

        if (is_array($direct) && ! empty($direct['product_slug'])) {
            return redirect()->route('product.detail', $direct['product_slug'])
                ->with('success', 'Beli langsung dibatalkan.');
        }

        return redirect()->route('cart.index')->with('success', 'Beli langsung dibatalkan.');
    }

    /**
     * Satu item beli langsung, terpisah dari isi keranjang.
     */
    private function directCheckoutRow(): ?array
    {
        $direct = session()->get('direct_checkout');

        if (! is_array($direct) || ! isset($direct['id_varian'], $direct['jumlah'])) {
            return null;
        }

        $variantId = (int) $direct['id_varian'];
        $quantity = (int) $direct['jumlah'];

        if ($variantId < 1 || $quantity < 1) {
            return null;
        }

        return [
            'id_varian' => $variantId,
            'jumlah' => $quantity,
        ];
    }

    /**
     * Ambil hanya item keranjang yang dipilih untuk checkout.
     * Jika belum ada pilihan, seluruh isi keranjang diproses.
     */
    private function applySelection(array $cart): array
    {
        $selection = session()->get('checkout_selection', []);

        if (empty($selection)) {
            return $cart;
        }

        $selectedIds = array_map('intval', $selection);

        return array_values(array_filter(
            $cart,
            static fn ($row) => in_array((int) $row['id_varian'], $selectedIds, true)
        ));
    }
}
