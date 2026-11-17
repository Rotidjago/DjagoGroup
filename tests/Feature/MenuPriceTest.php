<?php

namespace Tests\Feature;

use App\Enums\MenuCategory;
use App\Models\Menu;
use App\Models\MenuPrice;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MenuPriceTest extends TestCase
{
    use RefreshDatabase;

    private function menu(string $name = 'Keju'): Menu
    {
        return Menu::create(['name' => $name, 'category' => MenuCategory::Classic]);
    }

    private function price(Menu $menu, string $from, array $values): MenuPrice
    {
        return $menu->prices()->create(['effective_from' => $from] + $values);
    }

    /** Angka diambil dari tangkapan layar Penjualan_Harian (docs/referensi/sheet-penjualan-harian.png). */
    public function test_rumus_sama_dengan_google_sheets_contoh_keju(): void
    {
        $price = $this->price($this->menu('Keju'), '2026-03-30', [
            'price' => 21000, 'hpp' => 14542, 'seller_share' => 4000,
            'partner_share' => 600, 'debt_installment' => 200, 'coupon_budget' => 0,
        ]);

        $this->assertSame(6458, $price->gross_margin);
        $this->assertSame(1658, $price->gross_profit);
    }

    public function test_rumus_sama_dengan_google_sheets_contoh_kacang_coklat(): void
    {
        $price = $this->price($this->menu('Kacang Coklat'), '2026-03-30', [
            'price' => 22000, 'hpp' => 12289, 'seller_share' => 5000,
            'partner_share' => 600, 'debt_installment' => 600, 'coupon_budget' => 0,
        ]);

        $this->assertSame(9711, $price->gross_margin);
        $this->assertSame(3511, $price->gross_profit);
    }

    public function test_harga_yang_dipakai_sesuai_tanggal_penjualan(): void
    {
        $menu = $this->menu();
        $base = ['hpp' => 10000, 'seller_share' => 4000, 'partner_share' => 600, 'debt_installment' => 200, 'coupon_budget' => 0];

        $this->price($menu, '2026-03-01', ['price' => 21000] + $base);
        $this->price($menu, '2026-11-01', ['price' => 23000] + $base);

        $this->assertNull($menu->priceOn('2026-02-28'), 'Sebelum ada harga, hasilnya kosong');
        $this->assertSame(21000, $menu->priceOn('2026-03-01')->price);
        $this->assertSame(21000, $menu->priceOn('2026-10-31')->price);
        $this->assertSame(23000, $menu->priceOn('2026-11-01')->price);
        $this->assertSame(23000, $menu->priceOn('2026-12-25 23:59:00')->price);

        // Hasil sama ketika riwayat harga sudah dimuat lebih dulu (dipakai tabel admin).
        $menu->load('prices');
        $this->assertSame(21000, $menu->priceOn('2026-10-31')->price);
        $this->assertSame(23000, $menu->priceOn('2026-11-01')->price);
    }

    public function test_tanpa_tanggal_memakai_hari_ini_wib(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-17 23:30', 'Asia/Jakarta'));
        $menu = $this->menu();
        $base = ['hpp' => 1, 'seller_share' => 0, 'partner_share' => 0, 'debt_installment' => 0, 'coupon_budget' => 0];
        $this->price($menu, '2026-11-17', ['price' => 20000] + $base);
        $this->price($menu, '2026-11-18', ['price' => 25000] + $base);

        $this->assertSame(20000, $menu->priceOn()->price);

        Carbon::setTestNow();
    }

    public function test_tanggal_berlaku_disimpan_tanpa_jam(): void
    {
        $price = $this->price($this->menu(), '2026-03-30 15:45:00', [
            'price' => 1, 'hpp' => 1, 'seller_share' => 0, 'partner_share' => 0, 'debt_installment' => 0, 'coupon_budget' => 0,
        ]);

        $this->assertSame('2026-03-30', $price->getRawOriginal('effective_from'));
        $this->assertSame('2026-03-30', $price->fresh()->effective_from->toDateString());
    }

    public function test_satu_menu_tidak_boleh_punya_dua_harga_di_tanggal_yang_sama(): void
    {
        $menu = $this->menu();
        $values = ['price' => 1, 'hpp' => 1, 'seller_share' => 0, 'partner_share' => 0, 'debt_installment' => 0, 'coupon_budget' => 0];
        $this->price($menu, '2026-03-30', $values);

        $this->expectException(QueryException::class);
        $this->price($menu, '2026-03-30', $values);
    }
}
