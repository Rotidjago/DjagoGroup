<?php

namespace App\Filament\Resources\Materials\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama bahan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit')
                    ->label('Satuan')
                    ->placeholder('Belum diisi')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif saja')
                    ->falseLabel('Nonaktif saja'),
                TernaryFilter::make('has_unit')
                    ->label('Satuan')
                    ->trueLabel('Sudah diisi')
                    ->falseLabel('Belum diisi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('unit')->where('unit', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('unit')->orWhere('unit', '')),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
