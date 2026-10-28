<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_category_shows_all_products_without_next_page(): void
    {
        $seller = User::create([
            'name' => 'Penjual Katalog',
            'email' => 'penjual-katalog@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $store = $seller->store()->create([
            'nama_toko' => 'Toko Katalog',
            'kota_asal' => 'Bandung',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Batik',
        ]);

        for ($i = 1; $i <= 13; $i++) {
            Product::create([
                'id_toko' => $store->id_toko,
                'id_kategori' => $category->id_kategori,
                'nama_produk' => "Produk Semua {$i}",
                'harga_dasar' => 100000,
                'status' => 'aktif',
            ]);
        }

        $this->get('/katalog')
            ->assertOk()
            ->assertSee('13 produk ditemukan')
            ->assertSee('Produk Semua 1')
            ->assertSee('Produk Semua 13')
            ->assertDontSee('class="pagination"', false)
            ->assertDontSee('rel="next"', false);
    }
}
