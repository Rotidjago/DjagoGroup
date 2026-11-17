<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Harga dan pembagian per pcs untuk satu menu, berlaku mulai tanggal tertentu.
 *
 * Rumus (sama dengan Google Sheets):
 *   Margin Kotor = Harga − HPP
 *   Laba Kotor   = Margin Kotor − Bagi Hasil Penjual − Bagi Hasil Partner − Cicil Hutang − Budget Kupon
 */
#[Fillable([
    'menu_id', 'effective_from', 'price', 'hpp',
    'seller_share', 'partner_share', 'debt_installment', 'coupon_budget',
])]
class MenuPrice extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'hpp' => 'integer',
            'seller_share' => 'integer',
            'partner_share' => 'integer',
            'debt_installment' => 'integer',
            'coupon_budget' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Tanggal mulai berlaku selalu disimpan sebagai "YYYY-MM-DD" (tanpa jam),
     * supaya perbandingan tanggal dan aturan unik sama di SQLite maupun MySQL.
     */
    protected function effectiveFrom(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?Carbon => $value === null ? null : Carbon::parse($value)->startOfDay(),
            set: fn (mixed $value): ?string => $value === null ? null : Carbon::parse($value)->toDateString(),
        );
    }

    /** Margin Kotor = Harga − HPP */
    protected function grossMargin(): Attribute
    {
        return Attribute::get(fn (): int => $this->price - $this->hpp);
    }

    /** Laba Kotor = Margin Kotor − semua bagian (penjual, partner, cicilan, kupon) */
    protected function grossProfit(): Attribute
    {
        return Attribute::get(fn (): int => $this->gross_margin
            - $this->seller_share
            - $this->partner_share
            - $this->debt_installment
            - $this->coupon_budget);
    }
}
