<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('id_varian');
            $table->unsignedBigInteger('id_produk');
            $table->string('warna', 50);
            $table->string('ukuran', 20);
            $table->integer('stok')->default(0);
            $table->decimal('harga', 12, 2);
            $table->timestamps();

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
