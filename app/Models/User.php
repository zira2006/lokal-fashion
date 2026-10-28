<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password_hash',
        'role',
        'no_hp',
        'photo',
        'alamat',
        // Alias lama tetap diterima agar form dan kode lama tidak perlu berubah.
        'name',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if ($user->role !== 'admin') {
                return;
            }

            $user->admin()->updateOrCreate([], [
                'nama' => $user->nama,
                'email' => $user->email,
                'password_hash' => $user->password_hash,
            ]);
        });

        static::deleted(function (User $user) {
            $user->admin()->delete();
        });
    }

    public function getIdAttribute(): ?int
    {
        return $this->attributes['id_user'] ?? null;
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['no_hp'] = $value;
    }

    public function setPasswordAttribute(?string $value): void
    {
        $this->setAttribute('password_hash', $value);
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id_user', 'id_user');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id_user', 'id_user');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'id_user', 'id_user');
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class, 'id_user', 'id_user');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_user', 'id_user');
    }

    public function syncAlamatFromAddresses(): void
    {
        $alamat = $this->addresses()
            ->orderByDesc('is_default')
            ->orderBy('id_alamat')
            ->value('alamat_lengkap');

        $this->updateQuietly(['alamat' => $alamat]);
    }

    public function isSeller(): bool
    {
        return $this->role === 'penjual';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
