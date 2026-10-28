<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'ulasan';

    protected $primaryKey = 'id_ulasan';

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'id_user',
        'tgl',
        'rating',
        'komentar',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Review $review) {
            if (! $review->tgl) {
                $review->tgl = now();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_pesanan', 'id_pesanan');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
