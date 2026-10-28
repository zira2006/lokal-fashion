<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_varian');
            $table->integer('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['id_user', 'id_varian']);

            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('id_varian')
                ->references('id_varian')
                ->on('product_variants')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
