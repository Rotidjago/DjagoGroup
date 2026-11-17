<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama di layar')
                    ->helperText('Tulisan yang dilihat mitra, misalnya "GoFood".')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('sheet_value')
                    ->label('Nilai untuk Google Sheets')
                    ->helperText('Harus persis sama dengan pilihan di Sheets, misalnya "Gofood".')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Toggle::make('is_cash')
                    ->label('Uang tunai')
                    ->helperText('Aktifkan bila uangnya diterima mitra dan harus disetor ke pusat.'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
