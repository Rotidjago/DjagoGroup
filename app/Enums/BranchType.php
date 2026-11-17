<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BranchType: string implements HasLabel
{
    case Mitra = 'mitra';
    case Internal = 'internal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Mitra => 'Mitra',
            self::Internal => 'Internal',
        };
    }
}
