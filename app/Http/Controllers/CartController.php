<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use App\Support\CartStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = session()->get('cart', []);

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

            $subtotal = $variant->harga * $row['jumlah'];
            $total += $subtotal;

            $items[] = [
                'variant' => $variant,
                'jumlah' => $row['jumlah'],
                'subtotal' => $subtotal,
            ];
        }

        return view('storefront.cart', compact('items', 'total'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_varian' => ['required', 'exists:varian,id_varian'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $variant = Variant::whereHas('product', fn ($query) => $query->where('status', 'aktif')->dariPenjual())
            ->findOrFail($validated['id_varian']);

        if ($variant->stok < $validated['jumlah']) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = session()->get('cart', []);
        $existing = collect($cart)->firstWhere('id_varian', $validated['id_varian']);

        if ($existing) {
            $newQty = $existing['jumlah'] + $validated['jumlah'];
            if ($variant->stok < $newQty) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }
            foreach ($cart as &$row) {
                if ($row['id_varian'] == $validated['id_varian']) {
                    $row['jumlah'] = $newQty;
                }
            }
        } else {
            $cart[] = [
                'id_varian' => $validated['id_varian'],
                'jumlah' => $validated['jumlah'],
            ];
        }

        $this->persistCart($cart);
        session()->forget(['checkout_selection', 'direct_checkout']);

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_varian' => ['required'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        foreach ($cart as &$row) {
            if ($row['id_varian'] == $validated['id_varian']) {
                $variant = Variant::findOrFail($row['id_varian']);
                if ($variant->stok < $validated['jumlah']) {
                    return back()->with('error', 'Stok tidak mencukupi.');
                }
                $row['jumlah'] = $validated['jumlah'];
            }
        }

        $this->persistCart($cart);
        session()->forget(['checkout_selection', 'direct_checkout']);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_varian' => ['required'],
        ]);

        $cart = session()->get('cart', []);
        $cart = array_values(array_filter($cart, function ($row) use ($validated) {
            return $row['id_varian'] != $validated['id_varian'];
        }));

        $this->persistCart($cart);
        session()->forget(['checkout_selection', 'direct_checkout']);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget(['cart', 'checkout_selection', 'direct_checkout']);

        if (Auth::check()) {
            CartStorage::clearForUser((int) Auth::id());
            session()->put('cart_loaded_from_db', true);
        }

        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $validated = $request->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['integer'],
        ], [
            'selected.required' => 'Pilih minimal satu produk untuk checkout.',
            'selected.min' => 'Pilih minimal satu produk untuk checkout.',
        ]);

        $cartIds = array_map(static fn ($row) => (int) $row['id_varian'], $cart);
        $selected = array_map('intval', $validated['selected']);
        $selected = array_values(array_intersect($cartIds, $selected));

        if (empty($selected)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        session()->forget('direct_checkout');
        session()->put('checkout_selection', $selected);

        return redirect()->route('checkout');
    }

    /**
     * @param  array<int, array{id_varian: int, jumlah: int}>  $cart
     */
    private function persistCart(array $cart): void
    {
        if (Auth::check()) {
            $cart = CartStorage::replaceForUser((int) Auth::id(), $cart);
            session()->put('cart_loaded_from_db', true);
        }

        session()->put('cart', $cart);
    }
}
