<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_toko',
        'id_kategori',
        'kota_asal',
        'nama_produk',
        'deskripsi',
        'harga_dasar',
        'status',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'harga_dasar' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(
            Store::class,
            'id_toko',
            'id_toko'
        );
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'id_produk', 'id_produk');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_produk', 'id_produk');
    }

    public function totalStok(): int
    {
        return $this->variants->sum('stok');
    }

    /**
     * Produk yang berasal dari toko milik akun penjual.
     */
    public function scopeDariPenjual($query)
    {
        return $query->whereHas('store.user', fn ($userQuery) => $userQuery->where('role', 'penjual'));
    }

    /**
     * Filter kota asal produk: pakai kota khusus produk, lalu fallback ke kota toko.
     */
    public function scopeFilterByKota($query, string $kota)
    {
        return $query->where(function ($q) use ($kota) {
            $q->where('kota_asal', $kota)
                ->orWhere(function ($q2) use ($kota) {
                    $q2->where(function ($q3) {
                        $q3->whereNull('kota_asal')->orWhere('kota_asal', '');
                    })->whereHas('store', fn ($storeQuery) => $storeQuery->where('kota_asal', $kota));
                });
        });
    }

    /**
     * Kota asal produk. Prioritaskan kota asal khusus produk,
     * lalu fallback ke kota asal toko agar produk lama tetap tampil.
     */
    protected function asal(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->kota_asal ?: $this->store?->kota_asal);
    }

    /**
     * URL gambar produk yang aman dipakai di <img src="...">.
     * Mendukung URL eksternal, path lokal (images/...), maupun storage.
     */
    protected function gambarUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $gambar = trim((string) $this->gambar);

            if ($gambar === '') {
                return asset('images/product-placeholder.svg');
            }

            if (Str::startsWith($gambar, ['http://', 'https://', '//'])) {
                return $gambar;
            }

            $path = ltrim($gambar, '/');

            if (Str::startsWith($path, ['images/', 'storage/', 'uploads/'])) {
                return asset($path);
            }

            return asset('storage/'.$path);
        });
    }

    /**
     * Varian yang masih punya stok, dipakai untuk tombol beli cepat.
     */
    protected function varianTersedia(): Attribute
    {
        return Attribute::get(function () {
            return $this->variants->firstWhere('stok', '>', 0);
        });
    }
}
