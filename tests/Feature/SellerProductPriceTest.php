<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerProductPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_save_price_with_thousand_separator(): void
    {
        $seller = User::create([
            'name' => 'Penjual Harga',
            'email' => 'penjual-harga@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $seller->store()->create([
            'nama_toko' => 'Toko Harga',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Atasan',
        ]);

        $response = $this->actingAs($seller)->post(route('seller.products.store'), [
            'nama_produk' => 'Kemeja Test Harga',
            'id_kategori' => $category->id_kategori,
            'kota_asal' => 'Banda Aceh',
            'deskripsi' => 'Produk untuk pengujian harga.',
            'harga_dasar' => '200.000',
            'variants' => [
                [
                    'warna' => 'Merah',
                    'ukuran' => 'M',
                    'stok' => 5,
                    'harga' => '200.000',
                ],
            ],
        ]);

        $response->assertRedirect(route('seller.products'));

        $product = Product::where('nama_produk', 'Kemeja Test Harga')->firstOrFail();

        $this->assertSame('200000.00', $product->harga_dasar);
        $this->assertSame('200000.00', $product->variants()->firstOrFail()->harga);
    }
}
