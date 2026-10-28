<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $table = 'toko';

    protected $primaryKey = 'id_toko';

    protected $fillable = [
        'id_user',
        'id_admin',
        'nama_toko',
        'deskripsi',
        'kota_asal',
        'status_verifikasi',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_toko', 'id_toko');
    }
}
