<?php

namespace App\Enums\Admin;

enum AdminStatusEnum: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';

    /**
     * Get human-readable Uzbek label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::BLOCKED => 'Bloklangan',
        };
    }

    /**
     * Get Bootstrap badge class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-soft-success text-success',
            self::BLOCKED => 'bg-soft-danger text-danger',
        };
    }
}
