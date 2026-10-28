<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerAddressTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): User
    {
        return User::create([
            'name' => 'Pembeli Alamat',
            'email' => 'pembeli-alamat@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);
    }

    private function addressFor(User $user, array $overrides = []): Address
    {
        return Address::create(array_merge([
            'id_user' => $user->id,
            'nama_penerima' => 'Zira Zahira',
            'no_hp' => '081234567890',
            'alamat_lengkap' => "Jalan Mawar No. 1\nSigli",
            'kota' => 'Sigli',
            'kode_pos' => '2345',
            'is_default' => true,
        ], $overrides));
    }

    public function test_user_must_delete_existing_address_before_adding_a_new_one(): void
    {
        $user = $this->buyer();
        $this->addressFor($user);

        $response = $this->actingAs($user)
            ->from(route('profile.addresses'))
            ->post(route('profile.addresses.store'), [
                'nama_penerima' => 'Alamat Baru',
                'no_hp' => '081200000000',
                'alamat_lengkap' => 'Jalan Baru No. 2',
                'kota' => 'Banda Aceh',
                'kode_pos' => '23111',
            ]);

        $response->assertRedirect(route('profile.addresses'));
        $response->assertSessionHas('error', 'Hapus alamat lama terlebih dahulu sebelum menambahkan alamat baru.');
        $this->assertSame(1, Address::where('id_user', $user->id)->count());
    }

    public function test_address_used_by_an_order_is_soft_deleted_without_losing_order_history(): void
    {
        $user = $this->buyer();
        $address = $this->addressFor($user);

        $order = Order::create([
            'invoice' => 'INV-TEST-ALAMAT',
            'id_user' => $user->id,
            'id_alamat' => $address->id_alamat,
            'total' => 95000,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('profile.addresses.delete', $address->id_alamat));

        $response->assertRedirect(route('profile.addresses'));
        $this->assertSoftDeleted('addresses', ['id_alamat' => $address->id_alamat]);
        $this->assertSame($address->id_alamat, $order->fresh()->address?->id_alamat);
    }

    public function test_address_page_only_shows_the_address_form_when_the_user_has_no_address(): void
    {
        $user = $this->buyer();
        $address = $this->addressFor($user);

        $this->actingAs($user)
            ->get(route('profile.addresses'))
            ->assertOk()
            ->assertDontSee('Lengkapi Alamat Pengiriman')
            ->assertDontSee('Tambah Alamat');

        $this->actingAs($user)
            ->delete(route('profile.addresses.delete', $address->id_alamat))
            ->assertRedirect(route('profile.addresses'));

        $this->actingAs($user)
            ->get(route('profile.addresses'))
            ->assertOk()
            ->assertSee('Lengkapi Alamat Pengiriman')
            ->assertDontSee('Tambah Alamat');
    }

    public function test_user_can_add_a_new_address_after_deleting_the_previous_one(): void
    {
        $user = $this->buyer();
        $address = $this->addressFor($user);

        $this->actingAs($user)
            ->delete(route('profile.addresses.delete', $address->id_alamat))
            ->assertRedirect(route('profile.addresses'));

        $response = $this->actingAs($user)
            ->post(route('profile.addresses.store'), [
                'nama_penerima' => 'Alamat Baru',
                'no_hp' => '081200000000',
                'alamat_lengkap' => 'Jalan Baru No. 2',
                'kota' => 'Banda Aceh',
                'kode_pos' => '23111',
            ]);

        $response->assertRedirect(route('profile.addresses'));

        $newAddress = Address::where('id_user', $user->id)->firstOrFail();

        $this->assertSame('Alamat Baru', $newAddress->nama_penerima);
        $this->assertTrue($newAddress->is_default);
        $this->assertSame(1, Address::where('id_user', $user->id)->count());
        $this->assertSame(2, Address::withTrashed()->where('id_user', $user->id)->count());
    }
}
