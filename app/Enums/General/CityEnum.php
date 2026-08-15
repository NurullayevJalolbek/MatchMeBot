<?php

namespace App\Enums\General;

enum CityEnum: string
{
    case TASHKENT_CITY = 'tashkent_city';
    case TASHKENT_REGION = 'tashkent_region';
    case SAMARKAND = 'samarkand';
    case BUKHARA = 'bukhara';
    case ANDIJAN = 'andijan';
    case FERGANA = 'fergana';
    case NAMANGAN = 'namangan';
    case KASHKADARYA = 'kashkadarya';
    case SURKHANDARYA = 'surkhandarya';
    case KHOREZM = 'khorezm';
    case NAVOI = 'navoi';
    case JIZZAKH = 'jizzakh';
    case SIRDARYO = 'sirdaryo';
    case KARAKALPAKSTAN = 'karakalpakstan';

    /**
     * Get all enum values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a given string is a valid city enum value.
     */
    public static function isValid(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return in_array($value, self::values(), true);
    }

    /**
     * Get human-readable label in current language or default.
     */
    public function label(?string $locale = null): string
    {
        return __("enum.CityEnum.{$this->value}", [], $locale);
    }

    /**
     * Get all cities as key-value pairs for dropdown select.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
