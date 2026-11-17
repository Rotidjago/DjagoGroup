<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Enums\MenuCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama menu')
                    ->helperText('Tulis persis seperti nama resmi, termasuk huruf besar-kecil dan spasi.')
                    ->required()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state === null ? null : preg_replace('/\s+/', ' ', trim($state))),
                Select::make('category')
                    ->label('Kategori')
                    ->options(MenuCategory::class)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Menu nonaktif tidak muncul di pilihan mitra, tetapi riwayatnya tetap tersimpan.')
                    ->default(true),
            ]);
    }
}
