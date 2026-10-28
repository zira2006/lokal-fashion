<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_varian');
            $table->integer('jumlah');
            $table->decimal('harga', 12, 2);
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('id_varian')
                ->references('id_varian')
                ->on('product_variants')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
