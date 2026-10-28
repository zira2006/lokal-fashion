<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_search_clear_button_is_shown_when_searching(): void
    {
        $response = $this->get('/katalog?q=Syal');

        $response->assertOk();
        $response->assertSee('class="search-clear"', false);
        $response->assertSee('Hapus pencarian dan tampilkan semua produk', false);
    }

    public function test_search_clear_button_is_hidden_without_search(): void
    {
        $response = $this->get('/katalog');

        $response->assertOk();
        $response->assertDontSee('class="search-clear"', false);
    }

    public function test_buyer_pages_have_bottom_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Navigasi pembeli"', false);
        $response->assertSee('class="buyer-bottom-link', false);
        $response->assertSee('>Beranda</span>', false);
        $response->assertSee('>Profil</span>', false);
    }

    public function test_storefront_back_arrow_is_beside_the_brand(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder([
            'class="header-brand-group"',
            'back-button--brand',
            'class="brand"',
        ], false);
    }
}
