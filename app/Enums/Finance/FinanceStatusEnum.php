<?php

namespace App\Enums\Finance;

enum FinanceStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get human-readable Uzbek label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::INACTIVE => 'Nofaol',
        };
    }

    /**
     * Get Bootstrap badge class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-soft-success text-success',
            self::INACTIVE => 'bg-soft-danger text-danger',
        };
    }
}
