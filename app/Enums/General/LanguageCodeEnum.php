<?php

namespace App\Enums\General;

enum LanguageCodeEnum: string
{
    case UZ = 'uz';
    case RU = 'ru';
    case EN = 'en';

    /**
     * Get all enum values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a given string is a valid language code.
     */
    public static function isValid(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return in_array($value, self::values(), true);
    }
}
