<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): User
    {
        return User::create([
            'name' => 'Pembeli Pembayaran',
            'email' => 'pembeli-pembayaran@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);
    }

    private function addressFor(User $user): Address
    {
        return Address::create([
            'id_user' => $user->id,
            'nama_penerima' => 'Pembeli Pembayaran',
            'no_hp' => '081234567890',
            'alamat_lengkap' => 'Jalan Pembayaran No. 1',
            'kota' => 'Banda Aceh',
            'kode_pos' => '23111',
            'is_default' => true,
        ]);
    }

    private function orderFor(User $user, Address $address, array $overrides = []): Order
    {
        return Order::create(array_merge([
            'invoice' => 'INV-TEST-PAYMENT',
            'id_user' => $user->id,
            'id_alamat' => $address->id_alamat,
            'status' => 'baru',
            'total' => 200000,
        ], $overrides));
    }

    public function test_bank_payment_requires_a_connected_bank_account(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $response = $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'bank',
            'payment_provider' => 'BCA',
        ]);

        $response->assertSessionHasErrors('payment_provider');
        $this->assertSame('menunggu', $order->fresh()->status_pembayaran);
        $this->assertNull($order->fresh()->metode_pembayaran);
    }

    public function test_user_can_connect_a_bank_and_pay_with_it(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $connect = $this->actingAs($user)->post(route('payment.connect.store', $order->invoice), [
            'type' => 'bank',
            'provider' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Pembeli Pembayaran',
        ]);

        $connect->assertRedirect(route('payment.show', [
            'invoice' => $order->invoice,
            'method' => 'bank',
            'provider' => 'BCA',
        ]));
        $connect->assertSessionHas('success', 'Rekening BCA berhasil dihubungkan.');
        $this->assertDatabaseHas('payment_accounts', [
            'id_user' => $user->id,
            'type' => 'bank',
            'provider' => 'BCA',
            'account_number' => '1234567890',
        ]);

        $pay = $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'bank',
            'payment_provider' => 'BCA',
        ]);

        $pay->assertRedirect(route('orders.index'));

        $order->refresh();

        $this->assertSame('lunas', $order->status_pembayaran);
        $this->assertSame('diproses', $order->status);
        $this->assertSame('bank', $order->metode_pembayaran);
        $this->assertSame('BCA', $order->payment_provider);
        $this->assertSame('•••• 7890', $order->payment_account);
        $this->assertSame('Transfer Bank BCA', $order->paymentMethodLabel());

        $this->actingAs($user)
            ->get(route('orders.show', $order->invoice))
            ->assertOk()
            ->assertSee('Transfer Bank BCA')
            ->assertSee('•••• 7890');
    }

    public function test_user_can_connect_an_ewallet_and_pay_with_it(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)->post(route('payment.connect.store', $order->invoice), [
            'type' => 'ewallet',
            'provider' => 'DANA',
            'account_number' => '081234567890',
            'account_name' => 'Pembeli Pembayaran',
        ])->assertRedirect(route('payment.show', [
            'invoice' => $order->invoice,
            'method' => 'ewallet',
            'provider' => 'DANA',
        ]));

        $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'ewallet',
            'payment_provider' => 'DANA',
        ])->assertRedirect(route('orders.index'));

        $order->refresh();

        $this->assertSame('ewallet', $order->metode_pembayaran);
        $this->assertSame('DANA', $order->payment_provider);
        $this->assertSame('0812••••90', $order->payment_account);
        $this->assertSame('E-Wallet DANA', $order->paymentMethodLabel());
    }

    public function test_payment_page_shows_the_invoice_method_and_connected_account(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        PaymentAccount::create([
            'id_user' => $user->id,
            'type' => 'bank',
            'provider' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Pembeli Pembayaran',
        ]);

        $this->actingAs($user)
            ->get(route('payment.show', $order->invoice))
            ->assertOk()
            ->assertSee('Metode Pembayaran')
            ->assertSee('Transfer Bank BCA')
            ->assertSee('•••• 7890');
    }

    public function test_cod_payment_does_not_require_a_connected_account(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'cod',
        ])->assertRedirect(route('orders.index'));

        $order->refresh();

        $this->assertSame('menunggu', $order->status_pembayaran);
        $this->assertSame('diproses', $order->status);
        $this->assertSame('cod', $order->metode_pembayaran);
        $this->assertNull($order->payment_provider);
        $this->assertNull($order->payment_account);
        $this->assertSame('Bayar di Tempat (COD)', $order->paymentMethodLabel());
        $this->assertSame('Belum dibayar', $order->paymentStatusLabel());
    }

    public function test_cod_payment_becomes_paid_after_customer_confirms_receipt(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'cod',
        ])->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertSame('menunggu', $order->status_pembayaran);

        $order->update(['status' => 'dikirim']);

        $this->actingAs($user)
            ->get(route('orders.show', $order->invoice))
            ->assertOk()
            ->assertSee('Belum dibayar')
            ->assertSee('Pesanan Sudah Diterima');

        $this->actingAs($user)
            ->post(route('orders.receive', $order->invoice))
            ->assertRedirect();

        $order->refresh();

        $this->assertSame('selesai', $order->status);
        $this->assertSame('lunas', $order->status_pembayaran);

        $this->actingAs($user)
            ->get(route('orders.show', $order->invoice))
            ->assertOk()
            ->assertSee('Lunas');

        $this->actingAs($user)
            ->post(route('orders.receive', $order->invoice))
            ->assertRedirect();
    }

    public function test_confirming_receipt_marks_payment_as_paid_for_any_method(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user), [
            'invoice' => 'INV-TEST-RECEIVE-BANK',
            'status' => 'dikirim',
            'metode_pembayaran' => 'bank',
            'status_pembayaran' => 'menunggu',
        ]);

        $this->actingAs($user)
            ->post(route('orders.receive', $order->invoice))
            ->assertRedirect();

        $order->refresh();

        $this->assertSame('selesai', $order->status);
        $this->assertSame('lunas', $order->status_pembayaran);
    }

    public function test_seller_completing_cod_order_marks_payment_as_paid(): void
    {
        $seller = User::create([
            'name' => 'Penjual COD',
            'email' => 'penjual-cod@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $store = $seller->store()->create([
            'nama_toko' => 'Toko COD',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $buyer = $this->buyer();
        $address = $this->addressFor($buyer);

        $category = Category::create([
            'nama_kategori' => 'Atasan',
        ]);

        $product = Product::create([
            'id_toko' => $store->id_toko,
            'id_kategori' => $category->id_kategori,
            'nama_produk' => 'Kemeja COD',
            'deskripsi' => 'Produk untuk pengujian COD.',
            'harga_dasar' => 100000,
            'status' => 'aktif',
        ]);

        $variant = Variant::create([
            'id_produk' => $product->id_produk,
            'warna' => 'Hitam',
            'ukuran' => 'M',
            'stok' => 5,
            'harga' => 100000,
        ]);

        $order = $this->orderFor($buyer, $address, [
            'invoice' => 'INV-TEST-COD-SELLER',
            'status' => 'dikirim',
            'metode_pembayaran' => 'cod',
            'status_pembayaran' => 'menunggu',
            'total' => 100000,
        ]);

        OrderItem::create([
            'id_pesanan' => $order->id_pesanan,
            'id_varian' => $variant->id_varian,
            'jumlah' => 1,
            'harga' => 100000,
        ]);

        $this->actingAs($seller)
            ->post(route('seller.orders.update', $order->invoice), [
                'action' => 'simpan',
                'status' => 'selesai',
            ])
            ->assertRedirect();

        $order->refresh();

        $this->assertSame('selesai', $order->status);
        $this->assertSame('lunas', $order->status_pembayaran);
    }

    public function test_connect_page_is_only_for_valid_payment_providers(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)
            ->get(route('payment.connect.form', [
                'invoice' => $order->invoice,
                'type' => 'bank',
                'provider' => 'BCA',
            ]))
            ->assertOk()
            ->assertSee('Hubungkan Rekening Bank')
            ->assertSee('BCA');

        $this->actingAs($user)
            ->get(route('payment.connect.form', [
                'invoice' => $order->invoice,
                'type' => 'bank',
                'provider' => 'DANA',
            ]))
            ->assertNotFound();
    }

    public function test_payment_account_is_saved_for_the_authenticated_user(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)->post(route('payment.connect.store', $order->invoice), [
            'type' => 'bank',
            'provider' => 'BRI',
            'account_number' => '9876543210',
            'account_name' => 'Pembeli Pembayaran',
        ]);

        $account = PaymentAccount::where('id_user', $user->id)
            ->where('type', 'bank')
            ->where('provider', 'BRI')
            ->firstOrFail();

        $this->assertSame('•••• 3210', $account->masked_account_number);
    }

    public function test_registered_provider_without_connection_is_not_connected(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)
            ->get(route('payment.show', [
                'invoice' => $order->invoice,
                'method' => 'bank',
                'provider' => 'BCA',
            ]))
            ->assertOk()
            ->assertSee('Tidak terhubung')
            ->assertSee('Hubungkan rekening BCA dulu untuk melanjutkan.');
    }

    public function test_unknown_saved_provider_is_not_marked_as_connected(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        PaymentAccount::create([
            'id_user' => $user->id,
            'type' => 'bank',
            'provider' => 'Bank Asing',
            'account_number' => '1234567890',
            'account_name' => 'Pembeli Pembayaran',
        ]);

        config(['payments.providers.bank' => ['BRI']]);

        $this->actingAs($user)
            ->get(route('payment.show', [
                'invoice' => $order->invoice,
                'method' => 'bank',
                'provider' => 'Bank Asing',
            ]))
            ->assertOk()
            ->assertSee('Provider tidak ditemukan.')
            ->assertSee('Bank Asing tidak terdaftar di aplikasi.')
            ->assertSee('Akun tidak terhubung.')
            ->assertSee('Tidak terhubung')
            ->assertDontSee('•••• 7890');
    }

    public function test_unknown_provider_cannot_be_connected(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $response = $this->actingAs($user)->post(route('payment.connect.store', $order->invoice), [
            'type' => 'bank',
            'provider' => 'Bank Asing',
            'account_number' => '1234567890',
            'account_name' => 'Pembeli Pembayaran',
        ]);

        $response->assertSessionHasErrors('provider');
        $this->assertStringContainsString('tidak ditemukan di aplikasi', session('errors')->first('provider'));
        $this->assertStringContainsString('tidak terhubung', session('errors')->first('provider'));
        $this->assertDatabaseMissing('payment_accounts', [
            'id_user' => $user->id,
            'provider' => 'Bank Asing',
        ]);
    }

    public function test_unknown_provider_cannot_be_used_for_payment(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $response = $this->actingAs($user)->post(route('payment.pay', $order->invoice), [
            'metode_pembayaran' => 'bank',
            'payment_provider' => 'Bank Asing',
        ]);

        $response->assertSessionHasErrors('payment_provider');
        $this->assertStringContainsString('tidak ditemukan di aplikasi', session('errors')->first('payment_provider'));
        $this->assertStringContainsString('tidak terhubung', session('errors')->first('payment_provider'));
        $this->assertSame('menunggu', $order->fresh()->status_pembayaran);
        $this->assertNull($order->fresh()->metode_pembayaran);
    }

    public function test_unpaid_order_is_hidden_from_my_orders_until_payment_is_clicked(): void
    {
        $user = $this->buyer();
        $order = $this->orderFor($user, $this->addressFor($user));

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertDontSee($order->invoice);

        $this->actingAs($user)
            ->get(route('orders.show', $order->invoice))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('payment.show', $order->invoice))
            ->assertOk()
            ->assertSee('Pesanan baru akan muncul di Pesanan Saya');

        $this->actingAs($user)
            ->post(route('payment.pay', $order->invoice), [
                'metode_pembayaran' => 'cod',
            ])
            ->assertRedirect(route('orders.index'));

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee($order->invoice);

        $this->actingAs($user)
            ->get(route('orders.show', $order->invoice))
            ->assertOk()
            ->assertSee($order->invoice)
            ->assertSee('Belum dibayar');
    }
}
