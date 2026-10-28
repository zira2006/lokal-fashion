<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'store', 'variants'])
            ->where('status', 'aktif')
            ->dariPenjual();

        $q = $request->input('q');
        if (is_string($q) && trim($q) !== '') {
            $query->where('nama_produk', 'like', "%{$q}%");
        }

        $kategori = $request->input('kategori');
        $hasCategoryFilter = is_string($kategori) && $kategori !== '';

        if ($hasCategoryFilter) {
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

        $products = $hasCategoryFilter
            ? $query->latest()->paginate(12)->withQueryString()
            : $query->latest()->get();
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

        return view('storefront.catalog', compact('products', 'categories', 'cities'));
    }
}
