<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'no_invoice',
        'id_user',
        'id_alamat',
        'id_varian',
        'status',
        'metode_pembayaran',
        'payment_provider',
        'payment_account',
        'status_pembayaran',
        'resi',
        'kurir',
        'total_harga',
        'tgl_pesanan',
        'harga_satuan',
        'jumlah',
        // Alias lama tetap diterima agar kode dan pengujian lama tidak berubah.
        'invoice',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'harga_satuan' => 'decimal:2',
            'tgl_pesanan' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Order $order) {
            if (! $order->tgl_pesanan) {
                $order->tgl_pesanan = $order->created_at ?? now();
            }
        });

        static::created(function (Order $order) {
            $order->payment()->create([
                'tgl_bayar' => $order->status_pembayaran === 'lunas' ? now() : null,
                'metode' => $order->metode_pembayaran,
                'jumlah' => $order->total_harga,
                'status' => $order->status_pembayaran ?: 'menunggu',
                'provider' => $order->payment_provider,
                'account' => $order->payment_account,
            ]);

            $order->shipment()->create([
                'kurir' => $order->kurir,
                'no_resi' => $order->resi,
                'status' => $order->shipmentStatus(),
            ]);
        });

        static::updated(function (Order $order) {
            $payment = $order->payment()->first();

            $paymentAttributes = [
                'tgl_bayar' => $order->status_pembayaran === 'lunas'
                    ? ($payment?->tgl_bayar ?? now())
                    : null,
                'metode' => $order->metode_pembayaran,
                'jumlah' => $order->total_harga,
                'status' => $order->status_pembayaran ?: 'menunggu',
                'provider' => $order->payment_provider,
                'account' => $order->payment_account,
            ];

            if ($payment) {
                $payment->update($paymentAttributes);
            } else {
                $order->payment()->create($paymentAttributes);
            }

            $shipment = $order->shipment()->first();

            $shipmentAttributes = [
                'kurir' => $order->kurir,
                'no_resi' => $order->resi,
                'status' => $order->shipmentStatus(),
            ];

            if ($shipment) {
                $shipment->update($shipmentAttributes);
            } else {
                $order->shipment()->create($shipmentAttributes);
            }
        });
    }

    public function getInvoiceAttribute(): ?string
    {
        return $this->attributes['no_invoice'] ?? null;
    }

    public function setInvoiceAttribute(?string $value): void
    {
        $this->attributes['no_invoice'] = $value;
    }

    public function getTotalAttribute(): ?string
    {
        $value = $this->total_harga;

        return $value === null ? null : (string) $value;
    }

    public function setTotalAttribute($value): void
    {
        $this->setAttribute('total_harga', $value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'id_alamat', 'id_alamat')->withTrashed();
    }

    public function summaryVariant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'id_varian', 'id_varian');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'id_pesanan', 'id_pesanan');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'id_pesanan', 'id_pesanan');
    }

    public function shipmentStatus(): string
    {
        return match ($this->status) {
            'baru' => 'menunggu',
            'diproses' => 'diproses',
            'dikirim' => 'dikirim',
            'selesai' => 'diterima',
            'batal' => 'dibatalkan',
            default => $this->status ?: 'menunggu',
        };
    }

    public function paymentMethodLabel(): string
    {
        if (! $this->metode_pembayaran) {
            return 'Belum dipilih';
        }

        $label = config('payments.methods.'.$this->metode_pembayaran);

        if (! $label) {
            $label = $this->metode_pembayaran === 'transfer'
                ? 'Transfer Bank'
                : ucfirst($this->metode_pembayaran);
        }

        if ($this->payment_provider && in_array($this->metode_pembayaran, ['bank', 'transfer', 'ewallet'], true)) {
            $label .= ' '.$this->payment_provider;
        }

        return $label;
    }

    public function scopePlaced($query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('metode_pembayaran')
                ->orWhere('status_pembayaran', 'lunas');
        });
    }

    public function isCod(): bool
    {
        return $this->metode_pembayaran === 'cod';
    }

    public function paymentStatusLabel(): string
    {
        if ($this->status_pembayaran === 'lunas') {
            return 'Lunas';
        }

        if ($this->isCod()) {
            return 'Belum dibayar';
        }

        return $this->status_pembayaran
            ? ucfirst((string) $this->status_pembayaran)
            : 'Menunggu';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_pesanan', 'id_pesanan');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'id_pesanan', 'id_pesanan');
    }
}
