<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'semua');

        $query = Order::with(['address', 'items.variant.product'])
            ->where('id_user', Auth::id())
            ->placed();

        if (in_array($tab, ['baru', 'diproses', 'dikirim', 'selesai'])) {
            $query->where('status', $tab);
        }

        $orders = $query->latest()->get();

        return view('storefront.orders', compact('orders', 'tab'));
    }

    public function show(string $invoice): View
    {
        $order = Order::with(['address', 'items.variant.product.store', 'review'])
            ->where('no_invoice', $invoice)
            ->where('id_user', Auth::id())
            ->placed()
            ->firstOrFail();

        return view('storefront.order-detail', compact('order'));
    }

    public function receive(string $invoice): RedirectResponse
    {
        $order = Order::where('no_invoice', $invoice)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($order->status === 'selesai') {
            if ($order->status_pembayaran !== 'lunas') {
                $order->update(['status_pembayaran' => 'lunas']);
            }

            return back()->with('success', 'Pesanan sudah diterima. Status pembayaran lunas.');
        }

        abort_unless($order->status === 'dikirim', 404);

        $order->update([
            'status' => 'selesai',
            'status_pembayaran' => 'lunas',
        ]);

        return back()->with('success', 'Pesanan diterima. Status pembayaran telah menjadi lunas.');
    }

    public function storeReview(Request $request, string $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'id_produk' => ['required', 'exists:produk,id_produk'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::where('no_invoice', $invoice)
            ->where('id_user', Auth::id())
            ->where('status', 'selesai')
            ->placed()
            ->firstOrFail();

        if ($order->review) {
            return back()->with('error', 'Ulasan sudah dikirim untuk pesanan ini.');
        }

        Review::create([
            'id_pesanan' => $order->id_pesanan,
            'id_produk' => $validated['id_produk'],
            'id_user' => Auth::id(),
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih atas ulasannya!');
    }
}
