<?php

namespace App\Support;

/** Format dan baca angka rupiah gaya Indonesia (titik sebagai pemisah ribuan). */
class Rupiah
{
    /** 21000 → "Rp 21.000", -500 → "−Rp 500" */
    public static function format(?int $amount): string
    {
        if ($amount === null) {
            return '–';
        }

        $formatted = 'Rp '.number_format(abs($amount), 0, ',', '.');

        return $amount < 0 ? '−'.$formatted : $formatted;
    }

    /** "21.000" / "Rp 21.000" / 21000 → 21000. Nilai kosong menjadi 0. */
    public static function parse(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        $digits = preg_replace('/[^0-9-]/', '', (string) $value);

        return $digits === '' || $digits === '-' ? 0 : (int) $digits;
    }
}
