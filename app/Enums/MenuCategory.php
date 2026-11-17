<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuCategory: string implements HasLabel
{
    case Classic = 'classic';
    case Crunchy = 'crunchy';

    public function getLabel(): string
    {
        return match ($this) {
            self::Classic => 'Classic',
            self::Crunchy => 'Crunchy',
        };
    }

    /** Menu yang namanya mengandung "Crunchy" termasuk kategori Crunchy. */
    public static function guessFromName(string $name): self
    {
        return str_contains(mb_strtolower($name), 'crunchy') ? self::Crunchy : self::Classic;
    }
}
