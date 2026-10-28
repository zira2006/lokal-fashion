<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Registration;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ErdSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_erd_tables_and_columns_are_available(): void
    {
        foreach ([
            'admin',
            'toko',
            'produk',
            'kategori',
            'user',
            'registrasi',
            'varian',
            'keranjang',
            'pesanan',
            'pembayaran',
            'pengiriman',
            'ulasan',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} tidak ditemukan.");
        }

        // Tabel pendukung fitur web tetap tersedia.
        foreach (['addresses', 'order_items', 'payment_accounts'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel pendukung {$table} tidak ditemukan.");
        }

        $this->assertTrue(Schema::hasColumn('user', 'id_user'));
        $this->assertTrue(Schema::hasColumn('user', 'nama'));
        $this->assertTrue(Schema::hasColumn('user', 'password_hash'));
        $this->assertTrue(Schema::hasColumn('user', 'alamat'));
        $this->assertTrue(Schema::hasColumn('user', 'no_hp'));
        $this->assertTrue(Schema::hasColumn('user', 'created_at'));

        $this->assertTrue(Schema::hasColumn('admin', 'nama'));
        $this->assertTrue(Schema::hasColumn('admin', 'email'));
        $this->assertTrue(Schema::hasColumn('admin', 'password_hash'));

        $this->assertTrue(Schema::hasColumn('toko', 'id_toko'));
        $this->assertTrue(Schema::hasColumn('toko', 'id_user'));
        $this->assertTrue(Schema::hasColumn('toko', 'id_admin'));
        $this->assertTrue(Schema::hasColumn('toko', 'nama_toko'));
        $this->assertTrue(Schema::hasColumn('toko', 'status_verifikasi'));
        $this->assertTrue(Schema::hasColumn('toko', 'deskripsi'));
        $this->assertTrue(Schema::hasColumn('toko', 'kota_asal'));

        $this->assertTrue(Schema::hasColumn('produk', 'id_produk'));
        $this->assertTrue(Schema::hasColumn('produk', 'id_toko'));
        $this->assertTrue(Schema::hasColumn('produk', 'id_kategori'));
        $this->assertTrue(Schema::hasColumn('produk', 'nama_produk'));
        $this->assertTrue(Schema::hasColumn('produk', 'status'));
        $this->assertTrue(Schema::hasColumn('produk', 'deskripsi'));
        $this->assertTrue(Schema::hasColumn('produk', 'harga_dasar'));

        $this->assertTrue(Schema::hasColumn('kategori', 'id_admin'));
        $this->assertTrue(Schema::hasColumn('varian', 'sku'));

        $this->assertTrue(Schema::hasColumn('registrasi', 'id_registrasi'));
        $this->assertTrue(Schema::hasColumn('registrasi', 'id_user'));
        $this->assertTrue(Schema::hasColumn('registrasi', 'tgl_registrasi'));
        $this->assertTrue(Schema::hasColumn('registrasi', 'status_registrasi'));
        $this->assertTrue(Schema::hasColumn('registrasi', 'dokumen_identitas'));
        $this->assertTrue(Schema::hasColumn('registrasi', 'catatan_admin'));

        $this->assertTrue(Schema::hasColumn('keranjang', 'id_keranjang'));
        $this->assertTrue(Schema::hasColumn('keranjang', 'jumlah'));
        $this->assertTrue(Schema::hasColumn('keranjang', 'id_user'));
        $this->assertTrue(Schema::hasColumn('keranjang', 'id_varian'));

        $this->assertTrue(Schema::hasColumn('pesanan', 'no_invoice'));
        $this->assertTrue(Schema::hasColumn('pesanan', 'id_varian'));
        $this->assertTrue(Schema::hasColumn('pesanan', 'tgl_pesanan'));
        $this->assertTrue(Schema::hasColumn('pesanan', 'harga_satuan'));
        $this->assertTrue(Schema::hasColumn('pesanan', 'jumlah'));
        $this->assertTrue(Schema::hasColumn('pesanan', 'total_harga'));

        $this->assertTrue(Schema::hasColumn('pembayaran', 'id_bayar'));
        $this->assertTrue(Schema::hasColumn('pembayaran', 'id_pesanan'));
        $this->assertTrue(Schema::hasColumn('pembayaran', 'tgl_bayar'));
        $this->assertTrue(Schema::hasColumn('pembayaran', 'metode'));
        $this->assertTrue(Schema::hasColumn('pembayaran', 'jumlah'));
        $this->assertTrue(Schema::hasColumn('pembayaran', 'status'));

        $this->assertTrue(Schema::hasColumn('pengiriman', 'id_kirim'));
        $this->assertTrue(Schema::hasColumn('pengiriman', 'id_pesanan'));
        $this->assertTrue(Schema::hasColumn('pengiriman', 'kurir'));
        $this->assertTrue(Schema::hasColumn('pengiriman', 'no_resi'));
        $this->assertTrue(Schema::hasColumn('pengiriman', 'status'));

        $this->assertTrue(Schema::hasColumn('ulasan', 'tgl'));

        // Nama lama tidak lagi dipakai.
        $this->assertFalse(Schema::hasTable('users'));
        $this->assertFalse(Schema::hasTable('orders'));
        $this->assertFalse(Schema::hasColumn('user', 'name'));
        $this->assertFalse(Schema::hasColumn('user', 'password'));
        $this->assertFalse(Schema::hasColumn('user', 'phone'));
        $this->assertFalse(Schema::hasColumn('pesanan', 'invoice'));
        $this->assertFalse(Schema::hasColumn('pesanan', 'total'));
    }

    public function test_admin_record_is_filled_automatically(): void
    {
        $admin = User::create([
            'nama' => 'Admin ERD',
            'email' => 'admin-erd@example.com',
            'password_hash' => 'password',
            'role' => 'admin',
            'no_hp' => '081200001111',
        ]);

        $this->assertDatabaseHas('user', [
            'id_user' => $admin->id_user,
            'nama' => 'Admin ERD',
            'no_hp' => '081200001111',
        ]);

        $this->assertDatabaseHas('admin', [
            'id_user' => $admin->id_user,
            'nama' => 'Admin ERD',
            'email' => 'admin-erd@example.com',
        ]);
    }

    public function test_seller_registration_is_saved_to_registrasi_table(): void
    {
        $this->post('/daftar', [
            'name' => 'Penjual ERD',
            'email' => 'penjual-erd@example.com',
            'phone' => '081277778888',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'penjual',
        ])->assertRedirect(route('dashboard'));

        $seller = User::where('email', 'penjual-erd@example.com')->firstOrFail();

        $this->assertDatabaseHas('registrasi', [
            'id_user' => $seller->id_user,
            'status_registrasi' => 'menunggu',
            'dokumen_identitas' => null,
        ]);
    }

    public function test_admin_verification_updates_toko_and_registrasi(): void
    {
        $admin = User::create([
            'nama' => 'Admin Verifikasi',
            'email' => 'admin-verifikasi@example.com',
            'password_hash' => 'password',
            'role' => 'admin',
        ]);

        $seller = User::create([
            'nama' => 'Penjual Verifikasi',
            'email' => 'penjual-verifikasi@example.com',
            'password_hash' => 'password',
            'role' => 'penjual',
        ]);

        $store = Store::create([
            'id_user' => $seller->id_user,
            'nama_toko' => 'Toko Verifikasi',
            'status_verifikasi' => 'menunggu',
        ]);

        Registration::create([
            'id_user' => $seller->id_user,
            'tgl_registrasi' => now(),
            'status_registrasi' => 'menunggu',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.sellers.verify', $store->id_toko))
            ->assertRedirect();

        $this->assertDatabaseHas('toko', [
            'id_toko' => $store->id_toko,
            'status_verifikasi' => 'terverifikasi',
            'id_admin' => $admin->admin()->firstOrFail()->id_admin,
        ]);

        $this->assertDatabaseHas('registrasi', [
            'id_user' => $seller->id_user,
            'status_registrasi' => 'terverifikasi',
        ]);
    }

    public function test_order_creates_payment_shipment_and_item_summary(): void
    {
        [$buyer, $variant] = $this->buyerAndVariant();

        $address = Address::create([
            'id_user' => $buyer->id_user,
            'nama_penerima' => 'Pembeli ERD',
            'no_hp' => '081233334444',
            'alamat_lengkap' => 'Jl. ERD No. 1',
            'kota' => 'Banda Aceh',
            'kode_pos' => '23111',
            'is_default' => true,
        ]);

        $order = Order::create([
            'no_invoice' => 'INV-ERD-001',
            'id_user' => $buyer->id_user,
            'id_alamat' => $address->id_alamat,
            'status' => 'baru',
            'total_harga' => 150000,
        ]);

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $order->id_pesanan,
            'no_invoice' => 'INV-ERD-001',
            'total_harga' => 150000,
        ]);
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => $order->id_pesanan,
            'jumlah' => 150000,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('pengiriman', [
            'id_pesanan' => $order->id_pesanan,
            'status' => 'menunggu',
        ]);

        OrderItem::create([
            'id_pesanan' => $order->id_pesanan,
            'id_varian' => $variant->id_varian,
            'jumlah' => 2,
            'harga' => 75000,
        ]);

        $order->refresh();

        $this->assertSame($variant->id_varian, $order->id_varian);
        $this->assertSame(2, $order->jumlah);
        $this->assertSame('75000.00', $order->harga_satuan);
    }

    public function test_payment_and_shipment_follow_order_status_changes(): void
    {
        [$buyer] = $this->buyerAndVariant();

        $address = Address::create([
            'id_user' => $buyer->id_user,
            'nama_penerima' => 'Pembeli Status',
            'no_hp' => '081255556666',
            'alamat_lengkap' => 'Jl. Status No. 2',
            'kota' => 'Banda Aceh',
            'kode_pos' => '23112',
            'is_default' => true,
        ]);

        $order = Order::create([
            'no_invoice' => 'INV-ERD-002',
            'id_user' => $buyer->id_user,
            'id_alamat' => $address->id_alamat,
            'status' => 'baru',
            'total_harga' => 100000,
        ]);

        $order->update([
            'metode_pembayaran' => 'bank',
            'status_pembayaran' => 'lunas',
            'status' => 'diproses',
        ]);

        $payment = $order->payment()->firstOrFail();

        $this->assertSame('bank', $payment->metode);
        $this->assertSame('lunas', $payment->status);
        $this->assertNotNull($payment->tgl_bayar);

        $order->update([
            'status' => 'dikirim',
            'resi' => 'RESI-ERD-001',
            'kurir' => 'JNE',
        ]);

        $shipment = $order->shipment()->firstOrFail();

        $this->assertSame('dikirim', $shipment->status);
        $this->assertSame('RESI-ERD-001', $shipment->no_resi);
        $this->assertSame('JNE', $shipment->kurir);

        $order->update(['status' => 'selesai']);

        $this->assertSame('diterima', $order->shipment()->firstOrFail()->status);
    }

    public function test_variant_sku_and_review_date_are_generated(): void
    {
        [$buyer, $variant] = $this->buyerAndVariant();

        $this->assertNotNull($variant->sku);
        $this->assertStringStartsWith('SKU-', $variant->sku);

        $address = Address::create([
            'id_user' => $buyer->id_user,
            'nama_penerima' => 'Pembeli Ulasan',
            'no_hp' => '081277779999',
            'alamat_lengkap' => 'Jl. Ulasan No. 3',
            'kota' => 'Banda Aceh',
            'kode_pos' => '23113',
            'is_default' => true,
        ]);

        $order = Order::create([
            'no_invoice' => 'INV-ERD-003',
            'id_user' => $buyer->id_user,
            'id_alamat' => $address->id_alamat,
            'status' => 'selesai',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'lunas',
            'total_harga' => 75000,
        ]);

        $review = Review::create([
            'id_pesanan' => $order->id_pesanan,
            'id_produk' => $variant->id_produk,
            'id_user' => $buyer->id_user,
            'rating' => 5,
            'komentar' => 'Bagus',
        ]);

        $this->assertNotNull($review->tgl);
    }

    /**
     * @return array{0: User, 1: Variant}
     */
    private function buyerAndVariant(): array
    {
        $buyer = User::create([
            'nama' => 'Pembeli ERD',
            'email' => 'pembeli-erd@example.com',
            'password_hash' => 'password',
            'role' => 'pembeli',
        ]);

        $seller = User::create([
            'nama' => 'Penjual Toko ERD',
            'email' => 'toko-erd@example.com',
            'password_hash' => 'password',
            'role' => 'penjual',
        ]);

        $store = Store::create([
            'id_user' => $seller->id_user,
            'nama_toko' => 'Toko ERD',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Kategori ERD',
        ]);

        $product = Product::create([
            'id_toko' => $store->id_toko,
            'id_kategori' => $category->id_kategori,
            'nama_produk' => 'Produk ERD',
            'harga_dasar' => 75000,
            'status' => 'aktif',
        ]);

        $variant = Variant::create([
            'id_produk' => $product->id_produk,
            'warna' => 'Hitam',
            'ukuran' => 'M',
            'stok' => 10,
            'harga' => 75000,
        ]);

        return [$buyer, $variant];
    }
}
