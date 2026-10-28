<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $table = 'addresses';

    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_user',
        'nama_penerima',
        'no_hp',
        'alamat_lengkap',
        'kota',
        'kode_pos',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (Address $address) => $address->user?->syncAlamatFromAddresses());
        static::deleted(fn (Address $address) => $address->user?->syncAlamatFromAddresses());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_alamat', 'id_alamat');
    }
}
