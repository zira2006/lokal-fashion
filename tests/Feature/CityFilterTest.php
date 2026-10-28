<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_menu_is_available_in_catalog_and_home_sidebars(): void
    {
        $buyer = User::create([
            'name' => 'Pembeli Menu',
            'email' => 'pembeli-menu@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);

        foreach (['/katalog', '/'] as $path) {
            $this->actingAs($buyer)
                ->get($path)
                ->assertOk()
                ->assertSee('sidebar-account-menu', false)
                ->assertSeeInOrder([
                    'sidebar-account-menu',
                    'Profil Saya',
                    'Pesanan Saya',
                    'Alamat Pengiriman',
                    'Ganti Password',
                    'Keluar',
                    'Kategori',
                ], false);
        }
    }

    public function test_price_filter_and_apply_button_are_removed_from_home_and_catalog_sidebars(): void
    {
        foreach (['/katalog', '/'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('Kota UMKM')
                ->assertDontSee('>Harga<', false)
                ->assertDontSee('Terapkan Filter');
        }
    }

    public function test_city_filter_is_clickable_on_home_and_catalog(): void
    {
        $seller = User::create([
            'name' => 'Penjual Kota',
            'email' => 'penjual-kota@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $store = $seller->store()->create([
            'nama_toko' => 'Toko Kota',
            'kota_asal' => 'Banda Aceh',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Batik',
        ]);

        $product = Product::create([
            'id_toko' => $store->id_toko,
            'id_kategori' => $category->id_kategori,
            'nama_produk' => 'Batik Kota',
            'harga_dasar' => 100000,
            'status' => 'aktif',
        ]);

        Variant::create([
            'id_produk' => $product->id_produk,
            'warna' => 'Coklat',
            'ukuran' => 'M',
            'stok' => 5,
            'harga' => 100000,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('account-menu category-menu', false)
            ->assertSee(route('home', ['kategori' => $category->id_kategori]), false)
            ->assertSee(route('home', ['kota' => 'Banda Aceh']), false)
            ->assertSee('Banda Aceh')
            ->assertDontSee('type="radio" name="kota"', false);

        $categoryUrl = str_replace('&', '&amp;', route('catalog', [
            'kota' => 'Banda Aceh',
            'kategori' => $category->id_kategori,
        ]));

        $this->get(route('catalog', ['kota' => 'Banda Aceh']))
            ->assertOk()
            ->assertSee('account-menu category-menu', false)
            ->assertSee($categoryUrl, false)
            ->assertSee(route('catalog', ['kota' => 'Banda Aceh']), false)
            ->assertSee('Batik Kota')
            ->assertDontSee('type="radio" name="kota"', false);
    }
}
