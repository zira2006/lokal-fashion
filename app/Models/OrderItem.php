<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_pesanan',
        'id_varian',
        'jumlah',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (OrderItem $item) {
            $order = $item->order;

            if (! $order || $order->id_varian) {
                return;
            }

            // Ringkasan item pertama untuk kolom ERD. Detail semua item
            // tetap tersedia lengkap di tabel order_items.
            $order->updateQuietly([
                'id_varian' => $item->id_varian,
                'harga_satuan' => $item->harga,
                'jumlah' => $item->jumlah,
            ]);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_pesanan', 'id_pesanan');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'id_varian', 'id_varian');
    }
}
