<?php

namespace App\Enums\Subscription;

enum SubscriptionPeriodTypeEnum: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';

    /**
     * Human-readable label in Uzbek.
     */
    public function label(): string
    {
        return match ($this) {
            self::DAY => 'Kun',
            self::WEEK => 'Hafta',
            self::MONTH => 'Oy',
            self::YEAR => 'Yil',
        };
    }

    /**
     * Calculate approximate duration in days.
     */
    public function toDays(int $count = 1): int
    {
        return match ($this) {
            self::DAY => $count * 1,
            self::WEEK => $count * 7,
            self::MONTH => $count * 30,
            self::YEAR => $count * 365,
        };
    }
}
