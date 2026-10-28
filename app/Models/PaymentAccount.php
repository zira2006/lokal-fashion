<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccount extends Model
{
    protected $fillable = [
        'id_user',
        'type',
        'provider',
        'account_number',
        'account_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function getMaskedAccountNumberAttribute(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->account_number);

        if (strlen($digits) <= 4) {
            return (string) $this->account_number;
        }

        if ($this->type === 'ewallet') {
            return substr($digits, 0, 4).'••••'.substr($digits, -2);
        }

        return '•••• '.substr($digits, -4);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'bank' => 'rekening bank',
            'ewallet' => 'e-wallet',
            default => (string) $this->type,
        };
    }
}
