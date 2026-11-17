<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_aktif_bisa_membuka_semua_halaman_data_induk(): void
    {
        $this->seed(MasterDataSeeder::class);
        $admin = User::factory()->create();

        foreach (['/admin', '/admin/branches', '/admin/branches/create', '/admin/menus', '/admin/menus/1/edit', '/admin/materials', '/admin/payment-methods', '/admin/users'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_investor_tidak_bisa_membuka_panel_admin(): void
    {
        $this->actingAs(User::factory()->investor()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_nonaktif_tidak_bisa_membuka_panel_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => false]))
            ->get('/admin')
            ->assertForbidden();
    }
}
