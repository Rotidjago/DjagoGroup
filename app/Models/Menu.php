<?php

namespace App\Models;

use App\Enums\MenuCategory;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['name', 'category', 'is_active'])]
class Menu extends Model
{
    protected function casts(): array
    {
        return [
            'category' => MenuCategory::class,
            'is_active' => 'boolean',
        ];
    }

    /** Riwayat harga dan pembagian, terbaru dulu. */
    public function prices(): HasMany
    {
        return $this->hasMany(MenuPrice::class)->orderByDesc('effective_from');
    }

    /**
     * Harga dan pembagian yang berlaku pada tanggal tertentu (WIB),
     * yaitu baris dengan tanggal mulai berlaku terbaru yang tidak melewati tanggal itu.
     */
    public function priceOn(CarbonInterface|string|null $date = null): ?MenuPrice
    {
        $day = ($date === null ? Carbon::today() : Carbon::parse($date))->toDateString();

        // Kalau riwayat harga sudah dimuat (mis. di tabel admin), pakai itu tanpa query tambahan.
        if ($this->relationLoaded('prices')) {
            return $this->prices->first(
                fn (MenuPrice $price): bool => $price->effective_from->toDateString() <= $day
            );
        }

        return $this->prices()
            ->where('effective_from', '<=', $day)
            ->first();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
