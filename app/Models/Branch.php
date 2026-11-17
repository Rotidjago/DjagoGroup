<?php

namespace App\Models;

use App\Enums\BranchType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'type', 'address', 'is_active', 'notes'])]
class Branch extends Model
{
    protected function casts(): array
    {
        return [
            'type' => BranchType::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
