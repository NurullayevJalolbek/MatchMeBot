<?php

namespace App\Enums\Finance;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Inson tushunadigan o'zbekcha nom.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Kutilmoqda',
            self::APPROVED => 'Tasdiqlangan',
            self::REJECTED => 'Rad etilgan / Qaytarilgan',
        };
    }

    /**
     * Bootstrap badge klassi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-soft-warning text-warning',
            self::APPROVED => 'bg-soft-success text-success',
            self::REJECTED => 'bg-soft-danger text-danger',
        };
    }

    /**
     * Feather icon nomi.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'feather-clock',
            self::APPROVED => 'feather-check-circle',
            self::REJECTED => 'feather-x-circle',
        };
    }
}
