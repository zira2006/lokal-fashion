<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dashboard_shows_seller_menu(): void
    {
        $seller = User::create([
            'name' => 'Penjual Test',
            'email' => 'penjual-test@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $seller->store()->create([
            'nama_toko' => 'Toko Test',
            'status_verifikasi' => 'terverifikasi',
            'kota_asal' => 'Banda Aceh',
        ]);

        $response = $this->actingAs($seller)->get('/dashboard');

        $response->assertOk();
        $response->assertSeeInOrder([
            'class="sidebar-brand-row"',
            'back-button--sidebar',
            'class="brand">Lokal Fashion',
        ], false);
        $response->assertSeeInOrder([
            '>Dashboard</span>',
            '>Produk Saya</span>',
            '>Tambah Produk</span>',
            '>Pesanan</span>',
            '>Profil Toko</span>',
        ], false);
        $response->assertDontSee('Toko & Penjual', false);
    }

    public function test_seller_pages_have_dashboard_link_above_products(): void
    {
        $seller = User::create([
            'name' => 'Penjual Menu',
            'email' => 'penjual-menu@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $seller->store()->create([
            'nama_toko' => 'Toko Menu',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $this->actingAs($seller)
            ->get(route('seller.products'))
            ->assertOk()
            ->assertSeeInOrder([
                '>Dashboard</span>',
                '>Produk Saya</span>',
            ], false);
    }

    public function test_admin_dashboard_shows_admin_menu(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Pengguna');
        $response->assertSee('Toko & Penjual');
        $response->assertSee('Semua Pesanan');
        $response->assertSee('Total Pengguna');
    }

    public function test_buyer_can_logout_even_when_the_page_token_is_stale(): void
    {
        $buyer = User::create([
            'name' => 'Pembeli Logout',
            'email' => 'pembeli-logout@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);

        $this->actingAs($buyer)
            ->post('/keluar', ['_token' => 'stale-token'])
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_logout_is_safe_when_the_session_is_already_gone(): void
    {
        $this->post('/keluar')->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_session_expired_page_has_back_navigation(): void
    {
        $this->view('errors.419')
            ->assertSee('Sesi kamu sudah berakhir')
            ->assertSee('Kembali');
    }

    public function test_login_page_only_shows_buyer_and_admin_roles(): void
    {
        $response = $this->get('/masuk');

        $response->assertOk();
        $response->assertSee('data-role="pembeli"', false);
        $response->assertSee('data-role="admin"', false);
        $response->assertDontSee('data-role="penjual"', false);
    }

    public function test_admin_login_page_offers_seller_registration(): void
    {
        $response = $this->get('/masuk?role=admin');

        $response->assertOk();
        $response->assertSee('class="auth-tabs" id="authTabs"', false);
        $response->assertSee('data-register-role="penjual"', false);
        $response->assertSee('Daftar sebagai penjual');
        $response->assertSee('Belum punya akun penjual?');
    }

    public function test_seller_can_register_from_the_admin_login_page(): void
    {
        $this->get('/masuk?role=admin');

        $this->post('/daftar', [
            'name' => 'Penjual Baru',
            'email' => 'penjual-baru@example.com',
            'phone' => '08123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'penjual',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('user', [
            'email' => 'penjual-baru@example.com',
            'role' => 'penjual',
        ]);

        $this->assertDatabaseHas('toko', [
            'nama_toko' => 'Toko Penjual Baru',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_admin_account_cannot_be_registered(): void
    {
        $this->post('/daftar', [
            'name' => 'Admin Baru',
            'email' => 'admin-baru@example.com',
            'phone' => '08123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('user', [
            'email' => 'admin-baru@example.com',
        ]);
    }

    public function test_old_seller_login_link_opens_the_admin_login_tab(): void
    {
        $this->get('/masuk/penjual')
            ->assertRedirect(route('login', ['role' => 'admin']));
    }

    public function test_buyer_cannot_access_the_seller_and_admin_dashboard(): void
    {
        $buyer = User::create([
            'name' => 'Pembeli Test',
            'email' => 'pembeli-test@example.com',
            'password' => 'password',
            'role' => 'pembeli',
        ]);

        $this->actingAs($buyer)->get('/dashboard')->assertForbidden();
    }

    public function test_seller_login_through_buyer_tab_also_redirects_to_dashboard(): void
    {
        $seller = User::create([
            'name' => 'Penjual Pembeli Tab',
            'email' => 'penjual-pembeli-tab@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $seller->store()->create([
            'nama_toko' => 'Toko Pembeli Tab',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $this->post('/masuk', [
            'email' => $seller->email,
            'password' => 'password',
            'role' => 'pembeli',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_seller_and_admin_login_redirect_to_the_shared_dashboard(): void
    {
        $seller = User::create([
            'name' => 'Penjual Login',
            'email' => 'penjual-login@example.com',
            'password' => 'password',
            'role' => 'penjual',
        ]);

        $seller->store()->create([
            'nama_toko' => 'Toko Login',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $admin = User::create([
            'name' => 'Admin Login',
            'email' => 'admin-login@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->post('/masuk', [
            'email' => $seller->email,
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->post('/keluar');

        $this->post('/masuk', [
            'email' => $admin->email,
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_admin_area_redirects_to_the_shared_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Area',
            'email' => 'admin-area@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect(route('dashboard'));
    }
}
