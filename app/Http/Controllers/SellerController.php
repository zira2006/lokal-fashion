<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function profile(): View
    {
        $user = Auth::user();
        $store = $user->store;

        return view('seller.profile', compact('user', 'store'));
    }

    public function editProfile(): View
    {
        $user = Auth::user();
        $store = $user->store;

        return view('seller.profile-edit', compact('user', 'store'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $store = $user->store;

        $validated = $request->validate([
            'nama_toko' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kota_asal' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'min:6', 'confirmed'],
        ], [
            'photo.image' => 'File yang dipilih harus berupa gambar.',
            'photo.mimes' => 'Foto profil harus berformat JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $oldPhoto = $user->photo;
        $newPhoto = null;

        if ($request->hasFile('photo')) {
            $newPhoto = $this->storeUserPhoto($request->file('photo'));
        }

        $store->update([
            'nama_toko' => $validated['nama_toko'],
            'deskripsi' => $validated['deskripsi'] ?? $store->deskripsi,
            'kota_asal' => $validated['kota_asal'] ?? $store->kota_asal,
        ]);

        $user->update([
            'no_hp' => $validated['phone'] ?? $user->no_hp,
            'photo' => $newPhoto ?: $user->photo,
        ]);

        if ($validated['new_password'] ?? null) {
            $user->update(['password_hash' => $validated['new_password']]);
        }

        if ($newPhoto && $oldPhoto && str_starts_with($oldPhoto, 'uploads/profile/')) {
            File::delete(public_path($oldPhoto));
        }

        return redirect()->route('seller.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateProfilePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'Pilih foto terlebih dahulu.',
            'photo.image' => 'File yang dipilih harus berupa gambar.',
            'photo.mimes' => 'Foto profil harus berformat JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $user = $request->user();
        $oldPhoto = $user->photo;
        $newPhoto = $this->storeUserPhoto($request->file('photo'));

        $user->update(['photo' => $newPhoto]);

        if ($oldPhoto && str_starts_with($oldPhoto, 'uploads/profile/')) {
            File::delete(public_path($oldPhoto));
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function products(): View
    {
        $store = Auth::user()->store;

        $products = Product::with(['category', 'store', 'variants'])
            ->where('id_toko', $store->id_toko)
            ->latest()
            ->get();

        return view('seller.products', compact('products', 'store'));
    }

    public function createProduct(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $store = Auth::user()->store;

        return view('seller.product-create', compact('categories', 'store'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $this->normalizeRupiahInputs($request);

        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'kota_asal' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'harga_dasar' => ['required', 'numeric', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.warna' => ['required', 'string', 'max:50'],
            'variants.*.ukuran' => ['required', 'string', 'max:20'],
            'variants.*.stok' => ['required', 'integer', 'min:0'],
            'variants.*.harga' => ['required', 'numeric', 'min:0'],
            'variants.*.gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'kota_asal.required' => 'Kota asal produk wajib diisi.',
            'gambar.image' => 'File yang dipilih harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB.',
            'variants.*.gambar.image' => 'Gambar varian harus berupa file gambar.',
            'variants.*.gambar.mimes' => 'Gambar varian harus berformat JPG, PNG, atau WEBP.',
            'variants.*.gambar.max' => 'Ukuran gambar varian maksimal 2 MB.',
        ]);

        $store = Auth::user()->store;

        $savedImages = [];
        $gambarPath = null;

        if ($request->hasFile('gambar')) {
            $gambarPath = $this->storeProductImage($request->file('gambar'));
            $savedImages[] = $gambarPath;
        }

        DB::beginTransaction();

        try {
            $product = Product::create([
                'id_toko' => $store->id_toko,
                'id_kategori' => $validated['id_kategori'],
                'kota_asal' => $validated['kota_asal'],
                'nama_produk' => $validated['nama_produk'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'harga_dasar' => $validated['harga_dasar'],
                'gambar' => $gambarPath,
                'status' => 'aktif',
            ]);

            $primaryVariantImage = null;

            foreach ($validated['variants'] as $index => $v) {
                $variantImage = null;

                if ($request->hasFile("variants.{$index}.gambar")) {
                    $variantImage = $this->storeProductImage($request->file("variants.{$index}.gambar"));
                    $savedImages[] = $variantImage;

                    if (! $primaryVariantImage) {
                        $primaryVariantImage = $variantImage;
                    }
                }

                $product->variants()->create([
                    'warna' => $v['warna'],
                    'ukuran' => $v['ukuran'],
                    'stok' => $v['stok'],
                    'harga' => $v['harga'],
                    'gambar' => $variantImage,
                ]);
            }

            // Jika gambar utama tidak diunggah, pakai gambar varian pertama
            // agar kartu katalog tetap punya gambar.
            if (! $gambarPath && $primaryVariantImage) {
                $product->update(['gambar' => $primaryVariantImage]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($savedImages as $savedImage) {
                File::delete(public_path($savedImage));
            }

            return back()->with('error', 'Gagal menyimpan produk. '.$e->getMessage())->withInput();
        }

        return redirect()->route('seller.products')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct(string $id): View
    {
        $store = Auth::user()->store;
        $product = Product::with('variants')
            ->where('id_toko', $store->id_toko)
            ->where('id_produk', $id)
            ->firstOrFail();

        $categories = Category::orderBy('nama_kategori')->get();

        return view('seller.product-edit', compact('product', 'categories', 'store'));
    }

    public function updateProduct(Request $request, string $id): RedirectResponse
    {
        $this->normalizeRupiahInputs($request);

        $store = Auth::user()->store;
        $product = Product::with('variants')
            ->where('id_toko', $store->id_toko)
            ->where('id_produk', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'kota_asal' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'harga_dasar' => ['required', 'numeric', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.id_varian' => ['required', 'integer'],
            'variants.*.gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'kota_asal.required' => 'Kota asal produk wajib diisi.',
            'gambar.image' => 'File yang dipilih harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB.',
            'variants.*.gambar.image' => 'Gambar varian harus berupa file gambar.',
            'variants.*.gambar.mimes' => 'Gambar varian harus berformat JPG, PNG, atau WEBP.',
            'variants.*.gambar.max' => 'Ukuran gambar varian maksimal 2 MB.',
        ]);

        $variantInput = $validated['variants'] ?? [];
        unset($validated['variants']);

        $oldPrimaryImage = $product->gambar;
        $newFiles = [];
        $oldFilesToDelete = [];

        if ($request->hasFile('gambar')) {
            $newPrimaryImage = $this->storeProductImage($request->file('gambar'));
            $validated['gambar'] = $newPrimaryImage;
            $newFiles[] = $newPrimaryImage;

            if ($oldPrimaryImage) {
                $oldFilesToDelete[] = $oldPrimaryImage;
            }
        }

        DB::beginTransaction();

        try {
            $product->update($validated);

            foreach ($variantInput as $index => $variantData) {
                $variant = $product->variants()
                    ->where('id_varian', $variantData['id_varian'] ?? 0)
                    ->first();

                if (! $variant || ! $request->hasFile("variants.{$index}.gambar")) {
                    continue;
                }

                $oldVariantImage = $variant->gambar;
                $newVariantImage = $this->storeProductImage($request->file("variants.{$index}.gambar"));
                $newFiles[] = $newVariantImage;

                $variant->update(['gambar' => $newVariantImage]);

                if ($oldVariantImage) {
                    $oldFilesToDelete[] = $oldVariantImage;
                }

                if (! $product->gambar || $product->gambar === $oldVariantImage) {
                    $product->update(['gambar' => $newVariantImage]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($newFiles as $newFile) {
                File::delete(public_path($newFile));
            }

            throw $e;
        }

        foreach (array_unique(array_filter($oldFilesToDelete)) as $oldFile) {
            if (! str_starts_with($oldFile, 'uploads/products/')) {
                continue;
            }

            if (in_array($oldFile, $newFiles, true)) {
                continue;
            }

            if ($product->gambar === $oldFile) {
                continue;
            }

            if ($product->variants()->where('gambar', $oldFile)->exists()) {
                continue;
            }

            File::delete(public_path($oldFile));
        }

        return redirect()->route('seller.products')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduct(string $id): RedirectResponse
    {
        $store = Auth::user()->store;
        $product = Product::with('variants')
            ->where('id_toko', $store->id_toko)
            ->where('id_produk', $id)
            ->firstOrFail();

        if ($product->variants()->whereHas('orderItems')->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah memiliki pesanan. Nonaktifkan produk jika tidak ingin ditampilkan.');
        }

        $imagePaths = collect([$product->gambar])
            ->merge($product->variants->pluck('gambar'))
            ->filter(fn ($path) => $path && str_starts_with($path, 'uploads/products/'))
            ->unique()
            ->values();

        try {
            DB::transaction(function () use ($product) {
                $product->variants()->delete();
                $product->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Produk gagal dihapus. '.$e->getMessage());
        }

        foreach ($imagePaths as $imagePath) {
            File::delete(public_path($imagePath));
        }

        return redirect()->route('seller.products')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function toggleProductStatus(string $id): RedirectResponse
    {
        $store = Auth::user()->store;
        $product = Product::where('id_toko', $store->id_toko)
            ->where('id_produk', $id)
            ->firstOrFail();

        $product->update([
            'status' => $product->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status produk diperbarui.');
    }

    public function orders(): View
    {
        $store = Auth::user()->store;

        $orders = Order::with(['address', 'items.variant.product', 'user'])
            ->placed()
            ->whereHas('items.variant.product', function ($q) use ($store) {
                $q->where('id_toko', $store->id_toko);
            })
            ->latest()
            ->get();

        return view('seller.orders', compact('orders', 'store'));
    }

    public function updateOrderStatus(Request $request, string $invoice): RedirectResponse
    {
        $store = Auth::user()->store;
        $order = Order::where('no_invoice', $invoice)
            ->placed()
            ->whereHas('items.variant.product', function ($q) use ($store) {
                $q->where('id_toko', $store->id_toko);
            })
            ->firstOrFail();

        if ($request->input('action') === 'kirim_resi') {
            $validated = $request->validate([
                'resi' => ['required', 'string', 'max:100'],
                'kurir' => ['nullable', 'string', 'max:50'],
            ], [
                'resi.required' => 'No. resi wajib diisi sebelum dikirim ke pembeli.',
            ]);

            $order->update([
                'status' => 'dikirim',
                'resi' => $validated['resi'],
                'kurir' => $validated['kurir'] ?? $order->kurir,
            ]);

            return back()->with('success', "No. resi {$validated['resi']} berhasil dikirim ke pembeli.");
        }

        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,dikirim,selesai,batal'],
            'resi' => ['nullable', 'string', 'max:100'],
            'kurir' => ['nullable', 'string', 'max:50'],
        ]);

        $attributes = [
            'status' => $validated['status'],
            'resi' => $validated['resi'] ?? $order->resi,
            'kurir' => $validated['kurir'] ?? $order->kurir,
        ];

        if ($validated['status'] === 'selesai' && $order->isCod()) {
            $attributes['status_pembayaran'] = 'lunas';
        }

        $order->update($attributes);

        if ($validated['status'] === 'selesai' && $order->isCod()) {
            return back()->with('success', "Pesanan {$invoice} selesai. Pembayaran COD ditandai lunas.");
        }

        return back()->with('success', "Status pesanan {$invoice} diperbarui.");
    }

    private function normalizeRupiahInputs(Request $request): void
    {
        $request->merge([
            'harga_dasar' => $this->normalizeRupiah($request->input('harga_dasar')),
        ]);

        $variants = $request->input('variants');

        if (! is_array($variants)) {
            return;
        }

        foreach ($variants as $index => $variant) {
            if (is_array($variant) && array_key_exists('harga', $variant)) {
                $variants[$index]['harga'] = $this->normalizeRupiah($variant['harga']);
            }
        }

        $request->merge(['variants' => $variants]);
    }

    private function normalizeRupiah(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits === '' ? null : $digits;
    }

    private function storeUserPhoto(UploadedFile $file): string
    {
        $directory = public_path('uploads/profile');

        File::ensureDirectoryExists($directory);

        $filename = $file->hashName();
        $file->move($directory, $filename);

        return 'uploads/profile/'.$filename;
    }

    private function storeProductImage(UploadedFile $file): string
    {
        $directory = public_path('uploads/products');

        File::ensureDirectoryExists($directory);

        $filename = $file->hashName();
        $file->move($directory, $filename);

        return 'uploads/products/'.$filename;
    }
}
