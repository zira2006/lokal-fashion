<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->string('invoice', 30)->unique();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_alamat');
            $table->string('status', 30)->default('baru'); // baru, diproses, dikirim, selesai, batal
            $table->string('metode_pembayaran', 30)->nullable(); // transfer, ewallet, cod
            $table->string('status_pembayaran', 30)->default('menunggu'); // menunggu, lunas
            $table->string('resi', 100)->nullable();
            $table->string('kurir', 50)->nullable();
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('id_alamat')
                ->references('id_alamat')
                ->on('addresses')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
