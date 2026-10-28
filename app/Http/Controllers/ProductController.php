<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function home(Request $request): View
    {
        $query = Product::with(['category', 'store', 'variants'])
            ->where('status', 'aktif')
            ->dariPenjual();

        $q = $request->input('q');
        if (is_string($q) && trim($q) !== '') {
            $query->where('nama_produk', 'like', "%{$q}%");
        }

        $kategori = $request->input('kategori');
        if (is_string($kategori) && $kategori !== '') {
            $query->where('id_kategori', $kategori);
        }

        $kota = $request->input('kota');
        if (is_string($kota) && trim($kota) !== '') {
            $query->filterByKota($kota);
        }

        $hargaMin = $request->input('harga_min');
        if (is_numeric($hargaMin)) {
            $query->where('harga_dasar', '>=', $hargaMin);
        }

        $hargaMax = $request->input('harga_max');
        if (is_numeric($hargaMax)) {
            $query->where('harga_dasar', '<=', $hargaMax);
        }

        $products = $query->latest()->get();

        $categories = Category::orderBy('nama_kategori')->get();

        $cities = Product::with('store')
            ->where('status', 'aktif')
            ->dariPenjual()
            ->get()
            ->map(fn (Product $product) => $product->kota_asal ?: $product->store?->kota_asal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('home', compact('products', 'categories', 'cities'));
    }

    public function show(string $slug): View
    {
        $product = Product::with(['category', 'store', 'variants', 'reviews.user'])
            ->where('status', 'aktif')
            ->dariPenjual()
            ->whereRaw('LOWER(REPLACE(nama_produk, " ", "-")) = ?', [$slug])
            ->first();

        if (! $product) {
            $product = Product::with(['category', 'store', 'variants', 'reviews.user'])
                ->where('status', 'aktif')
                ->dariPenjual()
                ->get()
                ->first(fn (Product $item) => Str::slug($item->nama_produk) === $slug);
        }

        abort_if(! $product, 404);

        $reviews = Review::with('user')
            ->where('id_produk', $product->id_produk)
            ->latest()
            ->get();

        $avgRating = $reviews->avg('rating');

        return view('storefront.product-detail', compact('product', 'reviews', 'avgRating'));
    }
}
