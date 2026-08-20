<?php

namespace App\Enums\Finance;

enum ExpenseStatusEnum: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    /**
     * Inson tushunadigan o'zbekcha nom.
     */
    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Tasdiqlangan',
            self::PENDING => 'Kutilmoqda',
            self::CANCELLED => 'Bekor qilingan',
        };
    }

    /**
     * Bootstrap badge klassi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-soft-success text-success',
            self::PENDING => 'bg-soft-warning text-warning',
            self::CANCELLED => 'bg-soft-danger text-danger',
        };
    }

    /**
     * Feather icon nomi.
     */
    public function icon(): string
    {
        return match ($this) {
            self::APPROVED => 'feather-check-circle',
            self::PENDING => 'feather-clock',
            self::CANCELLED => 'feather-x-circle',
        };
    }
}
