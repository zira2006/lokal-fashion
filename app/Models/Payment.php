<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'id_bayar';

    protected $fillable = [
        'id_pesanan',
        'tgl_bayar',
        'metode',
        'jumlah',
        'status',
        'provider',
        'account',
    ];

    protected function casts(): array
    {
        return [
            'tgl_bayar' => 'datetime',
            'jumlah' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_pesanan', 'id_pesanan');
    }
}
