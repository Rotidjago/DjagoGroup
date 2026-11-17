<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case Admin = 'admin';
    case Investor = 'investor';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Admin pusat',
            self::Investor => 'Investor',
        };
    }
}
