<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BuyNowCartTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): User
    {
        return User::create([
            'name' => 'Pembeli Beli Langsung',
            'email' => 'pembeli-beli-langsung@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);
    }

    private function addressFor(User $buyer): Address
    {
        return Address::create([
            'id_user' => $buyer->id,
            'nama_penerima' => 'Pembeli Beli Langsung',
            'no_hp' => '081234567890',
            'alamat_lengkap' => 'Jalan Mawar No. 1',
            'kota' => 'Banda Aceh',
            'kode_pos' => '23111',
            'is_default' => true,
        ]);
    }

    /**
     * @return array{0: Product, 1: Variant, 2: Variant}
     */
    private function productWithTwoColors(): array
    {
        $seller = User::create([
            'name' => 'Penjual Warna',
            'email' => 'penjual-warna@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $store = $seller->store()->create([
            'nama_toko' => 'Toko Warna',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Atasan',
        ]);

        $product = Product::create([
            'id_toko' => $store->id_toko,
            'id_kategori' => $category->id_kategori,
            'nama_produk' => 'Kemeja Warna',
            'deskripsi' => 'Produk untuk pengujian beli langsung.',
            'harga_dasar' => 100000,
            'status' => 'aktif',
        ]);

        $red = Variant::create([
            'id_produk' => $product->id_produk,
            'warna' => 'Merah',
            'ukuran' => 'M',
            'stok' => 5,
            'harga' => 100000,
        ]);

        $blue = Variant::create([
            'id_produk' => $product->id_produk,
            'warna' => 'Biru',
            'ukuran' => 'L',
            'stok' => 5,
            'harga' => 110000,
        ]);

        return [$product, $red, $blue];
    }

    public function test_buy_now_uses_a_separate_session_and_does_not_add_to_cart(): void
    {
        $buyer = $this->buyer();
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->assertCount(1, session('cart'));

        $response = $this->actingAs($buyer)->post(route('checkout.buyNow'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 2,
        ]);

        $response->assertRedirect(route('checkout'));

        $direct = session('direct_checkout');

        $this->assertSame($blue->id_varian, $direct['id_varian']);
        $this->assertSame(2, $direct['jumlah']);
        $this->assertCount(1, session('cart'));
        $this->assertSame($red->id_varian, session('cart')[0]['id_varian']);
    }

    public function test_buy_now_order_does_not_consume_or_remove_cart_items(): void
    {
        $buyer = $this->buyer();
        $address = $this->addressFor($buyer);
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('checkout.buyNow'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 2,
        ])->assertRedirect(route('checkout'));

        $response = $this->actingAs($buyer)->post(route('checkout.store'), [
            'id_alamat' => $address->id_alamat,
        ]);

        $order = Order::latest('id_pesanan')->firstOrFail();

        $response->assertRedirect(route('payment.show', $order->invoice));

        $this->assertSame('220000.00', $order->total);
        $this->assertSame($blue->id_varian, $order->items()->firstOrFail()->id_varian);
        $this->assertSame(2, $order->items()->firstOrFail()->jumlah);
        $this->assertSame(3, $blue->fresh()->stok);
        $this->assertSame(5, $red->fresh()->stok);

        $this->assertNull(session('direct_checkout'));
        $this->assertCount(1, session('cart'));
        $this->assertSame($red->id_varian, session('cart')[0]['id_varian']);
        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);
    }

    public function test_different_colors_are_kept_as_separate_cart_rows(): void
    {
        $buyer = $this->buyer();
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $cart = session('cart');

        $this->assertCount(2, $cart);
        $this->assertEqualsCanonicalizing(
            [$red->id_varian, $blue->id_varian],
            array_column($cart, 'id_varian')
        );

        $this->actingAs($buyer)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Warna: Merah')
            ->assertSee('Warna: Biru')
            ->assertSee('Setiap warna dan ukuran ditampilkan sebagai baris terpisah.');
    }

    public function test_same_variant_is_merged_but_different_color_is_not(): void
    {
        $buyer = $this->buyer();
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ]);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $cart = session('cart');

        $this->assertCount(2, $cart);
        $this->assertSame(3, $cart[0]['jumlah']);
        $this->assertSame($blue->id_varian, $cart[1]['id_varian']);
    }

    public function test_checkout_page_shows_only_the_direct_item_and_can_cancel(): void
    {
        $buyer = $this->buyer();
        $this->addressFor($buyer);
        [$product, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('checkout.buyNow'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Beli Langsung')
            ->assertSee('Biru')
            ->assertDontSee('Merah')
            ->assertSee('Batalkan beli langsung')
            ->assertSee('form="cancelBuyNowForm"', false)
            ->assertSee('id="cancelBuyNowForm"', false);

        $this->actingAs($buyer)
            ->post(route('checkout.buyNow.cancel'))
            ->assertRedirect(route('product.detail', Str::slug($product->nama_produk)))
            ->assertSessionHas('success', 'Beli langsung dibatalkan.');

        $this->assertNull(session('direct_checkout'));
        $this->assertCount(1, session('cart'));
    }

    public function test_guest_buy_now_survives_login_and_reaches_checkout(): void
    {
        $buyer = $this->buyer();
        $this->addressFor($buyer);
        [, , $blue] = $this->productWithTwoColors();

        $this->post(route('checkout.buyNow'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ])->assertRedirect(route('checkout'));

        $this->get(route('checkout'))->assertRedirect(route('login'));

        $this->post('/masuk', [
            'email' => $buyer->email,
            'password' => 'password',
            'role' => 'pembeli',
        ])->assertRedirect(route('checkout'));

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSee('Beli Langsung')
            ->assertSee('Biru');
    }

    public function test_product_detail_uses_buy_now_without_cart_redirect(): void
    {
        $buyer = $this->buyer();
        [$product] = $this->productWithTwoColors();

        $this->actingAs($buyer)
            ->get(route('product.detail', Str::slug($product->nama_produk)))
            ->assertOk()
            ->assertSee('Tambah ke Keranjang')
            ->assertSee('Beli Sekarang')
            ->assertSee('formaction="'.route('checkout.buyNow').'"', false)
            ->assertDontSee('name="redirect"', false);
    }

    public function test_cart_checkout_overrides_a_pending_direct_checkout(): void
    {
        $buyer = $this->buyer();
        $this->addressFor($buyer);
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('checkout.buyNow'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.checkout'), [
            'selected' => [$red->id_varian],
        ])->assertRedirect(route('checkout'));

        $this->assertNull(session('direct_checkout'));
        $this->assertSame([$red->id_varian], session('checkout_selection'));

        $this->actingAs($buyer)
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Merah')
            ->assertDontSee('Biru')
            ->assertSee('Hanya produk yang kamu pilih di keranjang yang akan diproses.');
    }

    public function test_cart_survives_logout_and_login(): void
    {
        $buyer = $this->buyer();
        [, $red] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ]);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();

        $this->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'password',
            'role' => 'pembeli',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($buyer);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Kemeja Warna')
            ->assertSee('Warna: Merah')
            ->assertSee('data-qty="2"', false);

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ]);
    }

    public function test_guest_cart_is_merged_when_logging_in(): void
    {
        $buyer = $this->buyer();
        [, $red] = $this->productWithTwoColors();

        $this->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ])->assertRedirect();

        $this->assertGuest();
        $this->assertDatabaseMissing('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
        ]);

        $this->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'password',
            'role' => 'pembeli',
        ])->assertRedirect(route('home'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Warna: Merah')
            ->assertSee('data-qty="2"', false);

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
            'jumlah' => 2,
        ]);
    }

    public function test_cart_update_and_clear_are_persisted(): void
    {
        $buyer = $this->buyer();
        [, $red] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.update'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
            'jumlah' => 3,
        ]);

        $this->actingAs($buyer)->post(route('cart.clear'))->assertRedirect();

        $this->assertDatabaseMissing('keranjang', [
            'id_user' => $buyer->id,
        ]);
    }

    public function test_cart_drops_a_variant_that_was_deleted_before_saving(): void
    {
        $buyer = $this->buyer();
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $red->delete();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ])->assertRedirect();

        $cart = session('cart');

        $this->assertCount(1, $cart);
        $this->assertSame($blue->id_varian, $cart[0]['id_varian']);
        $this->assertSame(2, $cart[0]['jumlah']);

        $this->assertDatabaseMissing('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
        ]);

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $blue->id_varian,
            'jumlah' => 2,
        ]);
    }

    public function test_cart_checkout_removes_only_purchased_items_from_persistent_cart(): void
    {
        $buyer = $this->buyer();
        $address = $this->addressFor($buyer);
        [, $red, $blue] = $this->productWithTwoColors();

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $red->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)->post(route('cart.checkout'), [
            'selected' => [$red->id_varian],
        ])->assertRedirect(route('checkout'));

        $this->actingAs($buyer)->post(route('checkout.store'), [
            'id_alamat' => $address->id_alamat,
        ])->assertRedirect();

        $this->assertDatabaseMissing('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $red->id_varian,
        ]);

        $this->assertDatabaseHas('keranjang', [
            'id_user' => $buyer->id,
            'id_varian' => $blue->id_varian,
            'jumlah' => 1,
        ]);

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'password',
            'role' => 'pembeli',
        ])->assertRedirect(route('home'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Warna: Biru')
            ->assertDontSee('Warna: Merah');
    }
}
