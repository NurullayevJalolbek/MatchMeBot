<?php

namespace App\Enums\User;

enum BoostPlanEnum: string
{
    case HOURS_1 = '1_hour';
    case HOURS_3 = '3_hours';
    case HOURS_10 = '10_hours';

    public function hours(): int
    {
        return match ($this) {
            self::HOURS_1 => 1,
            self::HOURS_3 => 3,
            self::HOURS_10 => 10,
        };
    }

    public function price(): float
    {
        return match ($this) {
            self::HOURS_1 => 10000,
            self::HOURS_3 => 20000,
            self::HOURS_10 => 45000,
        };
    }

    public function originalPrice(): ?float
    {
        return match ($this) {
            self::HOURS_1 => null,
            self::HOURS_3 => 30000,
            self::HOURS_10 => 100000,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::HOURS_1 => '1 soatlik Boost',
            self::HOURS_3 => '3 soatlik Boost',
            self::HOURS_10 => '10 soatlik Super Boost',
        };
    }
}
