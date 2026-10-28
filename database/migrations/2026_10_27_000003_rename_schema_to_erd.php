<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename tabel dan kolom utama agar sama dengan entitas ERD.
     *
     * Tabel pendukung yang tidak ada di ERD (addresses, order_items,
     * payment_accounts) tetap dipertahankan karena dipakai fitur web.
     */
    public function up(): void
    {
        // Kolom duplikat dari penyelarasan ERD sebelumnya tidak diperlukan lagi
        // setelah kolom utama memakai nama ERD.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'password_hash', 'no_hp', 'email_verified_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'id_user');
            $table->renameColumn('name', 'nama');
            $table->renameColumn('password', 'password_hash');
            $table->renameColumn('phone', 'no_hp');
        });

        // orders: no_invoice dan total_harga sudah menjadi kolom utama.
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['no_invoice']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['no_invoice', 'total_harga']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['invoice']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('invoice', 'no_invoice');
            $table->renameColumn('total', 'total_harga');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('no_invoice');
        });

        // Nama tabel mengikuti nama entitas pada ERD.
        foreach ($this->tableMap() as $from => $to) {
            Schema::rename($from, $to);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tableMap(), true) as $from => $to) {
            Schema::rename($to, $from);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['no_invoice']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('no_invoice', 'invoice');
            $table->renameColumn('total_harga', 'total');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('invoice');
            $table->string('no_invoice', 30)->nullable()->unique();
            $table->decimal('total_harga', 12, 2)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
            $table->renameColumn('nama', 'name');
            $table->renameColumn('password_hash', 'password');
            $table->renameColumn('no_hp', 'phone');

            $table->string('nama', 100)->nullable();
            $table->string('password_hash')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    /**
     * @return array<string, string>
     */
    private function tableMap(): array
    {
        return [
            'admins' => 'admin',
            'stores' => 'toko',
            'products' => 'produk',
            'categories' => 'kategori',
            'users' => 'user',
            'registrations' => 'registrasi',
            'product_variants' => 'varian',
            'cart_items' => 'keranjang',
            'orders' => 'pesanan',
            'payments' => 'pembayaran',
            'shipments' => 'pengiriman',
            'reviews' => 'ulasan',
        ];
    }
};
