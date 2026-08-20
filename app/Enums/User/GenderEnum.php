<?php

namespace App\Enums\User;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case ALL = 'all';

    /**
     * Get human-readable label in Uzbek.
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Yigit',
            self::FEMALE => 'Qiz',
            self::ALL => 'Hamma',
        };
    }

    /**
     * Get biological/profile genders (excluding 'all').
     *
     * @return array<string>
     */
    public static function profileGenders(): array
    {
        return [
            self::MALE->value,
            self::FEMALE->value,
        ];
    }

    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
