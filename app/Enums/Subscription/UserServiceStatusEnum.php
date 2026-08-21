<?php

namespace App\Enums\Subscription;

enum UserServiceStatusEnum: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    /**
     * Inson tushunadigan o'zbekcha nom.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::EXPIRED => 'Muddati tugagan',
            self::CANCELLED => 'Bekor qilingan',
        };
    }

    /**
     * Bootstrap badge klassi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-soft-success text-success',
            self::EXPIRED => 'bg-soft-secondary text-secondary',
            self::CANCELLED => 'bg-soft-danger text-danger',
        };
    }

    /**
     * Feather icon nomi.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'feather-check-circle',
            self::EXPIRED => 'feather-clock',
            self::CANCELLED => 'feather-x-circle',
        };
    }
}
