<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Enums\BranchType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode cabang')
                    ->helperText('Kode singkat tanpa spasi, misalnya SRG untuk Soreang.')
                    ->required()
                    ->maxLength(20)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state === null ? null : mb_strtoupper(trim($state))),
                TextInput::make('name')
                    ->label('Nama cabang')
                    ->required()
                    ->maxLength(100),
                Select::make('type')
                    ->label('Tipe')
                    ->options(BranchType::class)
                    ->default(BranchType::Mitra)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Cabang nonaktif tidak bisa melapor, tetapi datanya tetap tersimpan.')
                    ->default(true),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
