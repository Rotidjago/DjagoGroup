<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Filament\Support\MoneyInput;
use App\Models\MenuPrice;
use App\Support\Rupiah;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

/**
 * Riwayat harga dan pembagian sebuah menu. Harga lama tidak ditimpa:
 * perubahan harga dicatat sebagai baris baru dengan tanggal mulai berlaku.
 */
class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Riwayat harga & pembagian';

    protected static ?string $modelLabel = 'harga';

    protected static ?string $pluralModelLabel = 'harga';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('effective_from')
                    ->label('Berlaku mulai')
                    ->helperText('Harga ini dipakai untuk penjualan mulai tanggal ini sampai ada harga baru.')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->default(fn (): string => today()->toDateString())
                    ->required()
                    // Satu menu hanya boleh punya satu harga per tanggal. Dibandingkan per TANGGAL
                    // (tanpa jam), karena isian tanggal bisa membawa jam dan aturan "unique" biasa
                    // lalu menganggapnya berbeda.
                    ->rule(fn (?MenuPrice $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                        $taken = MenuPrice::query()
                            ->where('menu_id', $this->getOwnerRecord()->getKey())
                            ->where('effective_from', Carbon::parse($value)->toDateString())
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->exists();

                        if ($taken) {
                            $fail('Menu ini sudah punya harga yang berlaku mulai tanggal tersebut. Ubah baris yang ada atau pilih tanggal lain.');
                        }
                    })
                    ->columnSpanFull(),
                MoneyInput::make('price', 'Harga')->live(onBlur: true),
                MoneyInput::make('hpp', 'HPP')->live(onBlur: true),
                MoneyInput::make('seller_share', 'Bagi hasil penjual')->live(onBlur: true),
                MoneyInput::make('partner_share', 'Bagi hasil partner (investor)')->live(onBlur: true),
                MoneyInput::make('debt_installment', 'Cicil hutang')->default(0)->live(onBlur: true),
                MoneyInput::make('coupon_budget', 'Budget kupon')->default(0)->live(onBlur: true),
                Section::make('Hasil hitungan otomatis')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('preview_gross_margin')
                            ->label('Margin kotor (Harga − HPP)')
                            ->state(fn (Get $get): string => Rupiah::format(self::marginFrom($get))),
                        TextEntry::make('preview_gross_profit')
                            ->label('Laba kotor pusat')
                            ->state(fn (Get $get): string => Rupiah::format(self::profitFrom($get)))
                            ->color(fn (Get $get): ?string => self::profitFrom($get) < 0 ? 'danger' : 'success'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('effective_from')
            ->defaultSort('effective_from', 'desc')
            ->emptyStateHeading('Belum ada harga')
            ->emptyStateDescription('Tambahkan harga dan pembagian sesuai sheet Data_Produk.')
            ->columns([
                TextColumn::make('effective_from')
                    ->label('Berlaku mulai')
                    ->date('d M Y')
                    // Dihitung saat tabel digambar, supaya langsung benar setelah harga ditambah/diubah.
                    ->description(fn (MenuPrice $record): ?string => match (true) {
                        $record->getKey() === $this->getOwnerRecord()->priceOn()?->getKey() => 'Berlaku sekarang',
                        $record->effective_from->isFuture() => 'Akan berlaku',
                        default => null,
                    }),
                self::money('price', 'Harga'),
                self::money('hpp', 'HPP'),
                self::money('gross_margin', 'Margin kotor'),
                self::money('seller_share', 'Penjual'),
                self::money('partner_share', 'Partner'),
                self::money('debt_installment', 'Cicil hutang'),
                self::money('coupon_budget', 'Kupon'),
                self::money('gross_profit', 'Laba kotor')
                    ->weight('medium')
                    ->color(fn (int $state): ?string => $state < 0 ? 'danger' : null),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah harga baru'),
            ])
            ->recordActions([
                EditAction::make(),
                // Hanya harga yang belum mulai berlaku yang boleh dihapus (riwayat tidak dihapus).
                DeleteAction::make()
                    ->visible(fn (MenuPrice $record): bool => $record->effective_from->isFuture()),
            ]);
    }

    private static function money(string $column, string $label): TextColumn
    {
        return TextColumn::make($column)
            ->label($label)
            ->formatStateUsing(fn (?int $state): string => Rupiah::format($state))
            ->alignEnd();
    }

    private static function marginFrom(Get $get): int
    {
        return Rupiah::parse($get('price')) - Rupiah::parse($get('hpp'));
    }

    private static function profitFrom(Get $get): int
    {
        return self::marginFrom($get)
            - Rupiah::parse($get('seller_share'))
            - Rupiah::parse($get('partner_share'))
            - Rupiah::parse($get('debt_installment'))
            - Rupiah::parse($get('coupon_budget'));
    }
}
