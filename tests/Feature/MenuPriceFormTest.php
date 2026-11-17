<?php

namespace Tests\Feature;

use App\Enums\MenuCategory;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\RelationManagers\PricesRelationManager;
use App\Models\Menu;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/** Uji formulir "Tambah harga baru" di halaman ubah menu (panel admin). */
class MenuPriceFormTest extends TestCase
{
    use RefreshDatabase;

    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
        $this->menu = Menu::create(['name' => 'Keju', 'category' => MenuCategory::Classic]);
    }

    private function form(): Testable
    {
        return Livewire::test(PricesRelationManager::class, [
            'ownerRecord' => $this->menu,
            'pageClass' => EditMenu::class,
        ]);
    }

    /** Isian seperti yang diketik admin: memakai titik ribuan. */
    private function data(string $date, string $price = '21.000'): array
    {
        return [
            'effective_from' => $date,
            'price' => $price,
            'hpp' => '14.542',
            'seller_share' => '4.000',
            'partner_share' => '600',
            'debt_installment' => '200',
            'coupon_budget' => '0',
        ];
    }

    public function test_harga_baru_tersimpan_tanpa_titik_ribuan(): void
    {
        $this->form()
            ->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'))
            ->assertHasNoFormErrors();

        $price = $this->menu->prices()->sole();
        $this->assertSame(21000, $price->price);
        $this->assertSame(14542, $price->hpp);
        $this->assertSame(1658, $price->gross_profit);
    }

    public function test_tanggal_ganda_ditolak_dengan_pesan_yang_jelas(): void
    {
        $this->form()->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'));

        $this->form()
            ->callAction(TestAction::make('create')->table(), $this->data('2026-03-30', '22.000'))
            ->assertHasFormErrors(['effective_from']);

        $this->assertSame(1, $this->menu->prices()->count());
    }

    /** Meniru kejadian di browser: tanggal bawaan formulir ikut membawa jam. */
    public function test_tanggal_ganda_tetap_ditolak_walaupun_isian_membawa_jam(): void
    {
        $this->form()->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'));

        $this->form()
            ->callAction(TestAction::make('create')->table(), $this->data('2026-03-30 08:05:52', '22.000'))
            ->assertHasFormErrors(['effective_from']);

        $this->assertSame(1, $this->menu->prices()->count());
    }

    public function test_tanggal_bawaan_formulir_adalah_hari_ini_dan_ganda_ditolak(): void
    {
        $this->travelTo(now()->setTime(8, 5, 52));
        $withoutDate = fn (string $price): array => array_diff_key($this->data('x', $price), ['effective_from' => true]);

        $this->form()
            ->callAction(TestAction::make('create')->table(), $withoutDate('21.000'))
            ->assertHasNoFormErrors();
        $this->assertSame(today()->toDateString(), $this->menu->prices()->sole()->effective_from->toDateString());

        $this->form()
            ->callAction(TestAction::make('create')->table(), $withoutDate('22.000'))
            ->assertHasFormErrors(['effective_from']);
    }

    public function test_tanggal_sama_di_menu_lain_tetap_boleh(): void
    {
        $this->form()->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'));

        $other = Menu::create(['name' => 'Coklat', 'category' => MenuCategory::Classic]);
        Livewire::test(PricesRelationManager::class, ['ownerRecord' => $other, 'pageClass' => EditMenu::class])
            ->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'))
            ->assertHasNoFormErrors();
    }

    public function test_mengubah_harga_tanpa_ganti_tanggal_tidak_dianggap_ganda(): void
    {
        $this->form()->callAction(TestAction::make('create')->table(), $this->data('2026-03-30'));
        $price = $this->menu->prices()->sole();

        $this->form()
            ->callAction(TestAction::make('edit')->table($price), $this->data('2026-03-30', '21.500'))
            ->assertHasNoFormErrors();

        $this->assertSame(21500, $price->fresh()->price);
    }
}
