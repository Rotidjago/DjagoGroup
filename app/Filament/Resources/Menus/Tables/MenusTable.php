<?php

namespace App\Filament\Resources\Menus\Tables;

use App\Enums\MenuCategory;
use App\Models\Menu;
use App\Support\Rupiah;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('prices'))
            ->defaultSort('name')
            ->paginationPageOptions([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama menu')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('current_price')
                    ->label('Harga sekarang')
                    ->state(fn (Menu $record): ?int => $record->priceOn()?->price)
                    ->formatStateUsing(fn (?int $state): string => Rupiah::format($state))
                    ->placeholder('Belum ada harga')
                    ->alignEnd(),
                TextColumn::make('current_seller_share')
                    ->label('Bagi hasil penjual')
                    ->state(fn (Menu $record): ?int => $record->priceOn()?->seller_share)
                    ->formatStateUsing(fn (?int $state): string => Rupiah::format($state))
                    ->placeholder('–')
                    ->alignEnd()
                    ->toggleable(),
                TextColumn::make('current_gross_profit')
                    ->label('Laba kotor')
                    ->state(fn (Menu $record): ?int => $record->priceOn()?->gross_profit)
                    ->formatStateUsing(fn (?int $state): string => Rupiah::format($state))
                    ->color(fn (?int $state): ?string => $state !== null && $state < 0 ? 'danger' : null)
                    ->placeholder('–')
                    ->alignEnd()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(MenuCategory::class),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif saja')
                    ->falseLabel('Nonaktif saja'),
                TernaryFilter::make('has_price')
                    ->label('Harga')
                    ->trueLabel('Sudah ada harga')
                    ->falseLabel('Belum ada harga')
                    ->queries(
                        true: fn (Builder $query) => $query->has('prices'),
                        false: fn (Builder $query) => $query->doesntHave('prices'),
                    ),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah / atur harga'),
            ]);
    }
}
