<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Variant extends Model
{
    protected $table = 'varian';

    protected $primaryKey = 'id_varian';

    protected $fillable = [
        'id_produk',
        'sku',
        'warna',
        'ukuran',
        'stok',
        'harga',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Variant $variant) {
            if (filled($variant->sku)) {
                return;
            }

            do {
                $sku = 'SKU-'.$variant->id_produk.'-'.Str::upper(Str::random(6));
            } while (static::where('sku', $sku)->exists());

            $variant->sku = $sku;
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_varian', 'id_varian');
    }

    /**
     * URL gambar khusus varian/warna.
     */
    protected function gambarUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $gambar = trim((string) $this->gambar);

            if ($gambar === '') {
                return null;
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
}
