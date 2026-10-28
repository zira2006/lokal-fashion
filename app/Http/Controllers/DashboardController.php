<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $stats = [
                'total_users' => User::whereIn('role', ['pembeli', 'penjual'])->count(),
                'total_sellers' => User::where('role', 'penjual')->count(),
                'total_buyers' => User::where('role', 'pembeli')->count(),
                'total_orders' => Order::placed()->count(),
                'total_products' => Product::where('status', 'aktif')->dariPenjual()->count(),
                'total_stores' => Store::whereHas('user', fn ($query) => $query->where('role', 'penjual'))->count(),
                'pending_stores' => Store::whereHas('user', fn ($query) => $query->where('role', 'penjual'))
                    ->where('status_verifikasi', 'menunggu')
                    ->count(),
                'recent_orders' => Order::placed()->with('user')->latest()->take(5)->get(),
                'recent_users' => User::whereIn('role', ['pembeli', 'penjual'])->latest()->take(5)->get(),
            ];

            return view('dashboard', [
                'role' => 'admin',
                'stats' => $stats,
            ]);
        }

        $store = $user->store;
        $orders = Order::query()
            ->placed()
            ->whereHas('items.variant.product', function ($query) use ($store) {
                $query->where('id_toko', $store->id_toko);
            });

        $stats = [
            'active_products' => Product::where('id_toko', $store->id_toko)->where('status', 'aktif')->count(),
            'new_orders' => (clone $orders)->where('status', 'baru')->count(),
            'processing_orders' => (clone $orders)->where('status', 'diproses')->count(),
            'shipped_orders' => (clone $orders)->where('status', 'dikirim')->count(),
        ];

        $recentOrders = (clone $orders)
            ->with(['user', 'address', 'items.variant.product'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'role' => 'penjual',
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
