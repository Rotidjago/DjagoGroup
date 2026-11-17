<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Menu;
use App\Models\PaymentMethod;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_mengisi_menu_bahan_dan_metode_bayar(): void
    {
        $this->seed(MasterDataSeeder::class);

        $this->assertSame(count(MasterDataSeeder::MENUS), Menu::count());
        $this->assertSame(count(MasterDataSeeder::MATERIALS), Material::count());
        $this->assertSame(6, PaymentMethod::count());
        $this->assertSame(['Tunai'], PaymentMethod::where('is_cash', true)->pluck('sheet_value')->all());
        $this->assertSame('Gofood', PaymentMethod::where('name', 'GoFood')->value('sheet_value'));
    }

    public function test_aman_dijalankan_dua_kali_dan_tidak_menimpa_perubahan_admin(): void
    {
        $this->seed(MasterDataSeeder::class);
        Material::where('name', 'Gas')->update(['unit' => 'tabung']);

        $this->seed(MasterDataSeeder::class);

        $this->assertSame(count(MasterDataSeeder::MENUS), Menu::count());
        $this->assertSame('tabung', Material::where('name', 'Gas')->value('unit'));
    }

    public function test_nama_tidak_ganda_dan_tanpa_spasi_berlebih(): void
    {
        $names = array_merge(MasterDataSeeder::MENUS, array_keys(MasterDataSeeder::MATERIALS));

        foreach ($names as $name) {
            $this->assertSame(preg_replace('/\s+/', ' ', trim($name)), $name, "Spasi berlebih pada \"{$name}\"");
        }

        $this->assertSame(count(MasterDataSeeder::MENUS), count(array_unique(MasterDataSeeder::MENUS)));
    }

    public function test_kategori_crunchy_ditebak_dari_nama(): void
    {
        $this->seed(MasterDataSeeder::class);

        $this->assertSame('crunchy', Menu::where('name', 'Choco Crunchy Keju')->value('category')->value);
        $this->assertSame('classic', Menu::where('name', 'Coklat Keju + Buah')->value('category')->value);
    }
}
