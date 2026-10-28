<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('admins')->orderBy('id_admin')->value('id_admin');

        if (! $adminId) {
            return;
        }

        DB::table('stores')
            ->whereNull('id_admin')
            ->update(['id_admin' => $adminId]);
    }

    public function down(): void
    {
        // Data hasil verifikasi admin tidak dikembalikan menjadi null.
    }
};
