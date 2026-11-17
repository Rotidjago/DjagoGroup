<?php

namespace App\Filament\Resources\Materials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama bahan')
                    ->required()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state === null ? null : preg_replace('/\s+/', ' ', trim($state))),
                TextInput::make('unit')
                    ->label('Satuan')
                    ->helperText('Contoh: pcs, kg, pack. Ditampilkan ke mitra sebagai info.')
                    ->maxLength(20)
                    ->datalist(['pcs', 'kg', 'gram', 'liter', 'pack', 'dus', 'botol', 'tabung', 'lembar', 'roll']),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Bahan nonaktif tidak muncul di pilihan order mitra.')
                    ->default(true),
            ]);
    }
}
