<?php

namespace App\Enums\Deposit;

enum DepositStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Kutilmoqda',
            self::APPROVED => 'Tasdiqlangan',
            self::REJECTED => 'Rad etilgan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
