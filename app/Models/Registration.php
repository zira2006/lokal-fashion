<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $table = 'registrasi';

    protected $primaryKey = 'id_registrasi';

    protected $fillable = [
        'id_user',
        'tgl_registrasi',
        'status_registrasi',
        'dokumen_identitas',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tgl_registrasi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
