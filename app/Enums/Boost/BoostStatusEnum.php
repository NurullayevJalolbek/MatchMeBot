<?php

namespace App\Enums\Boost;

enum BoostStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get label in Uzbek.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::INACTIVE => 'Nofaol',
        };
    }

    /**
     * Get badge color class for UI.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-soft-success text-success',
            self::INACTIVE => 'bg-soft-danger text-danger',
        };
    }

    /**
     * Get all values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
