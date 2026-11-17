<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        // Admin tidak boleh menonaktifkan atau menurunkan perannya sendiri,
        // supaya tidak terkunci dari panel.
        $isSelf = fn (?User $record): bool => $record !== null && $record->is(auth()->user());

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(fn (string $operation): string => $operation === 'create' ? 'Kata sandi' : 'Kata sandi baru')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Kosongkan bila tidak ingin mengganti.' : 'Minimal 10 karakter.')
                    ->password()
                    ->revealable()
                    ->rule(Password::min(10))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->label('Peran')
                    ->options(UserRole::class)
                    ->default(UserRole::Admin)
                    ->required()
                    ->disabled($isSelf),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Pengguna nonaktif tidak bisa masuk.')
                    ->default(true)
                    ->disabled($isSelf),
            ]);
    }
}
