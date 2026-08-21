<?php

namespace App\Enums\Profile;

enum ProfileOptionTypeEnum: string
{
    case INTEREST = 'interest';
    case DATING_PURPOSE = 'dating_purpose';
    case LIFESTYLE = 'lifestyle';
    case ABOUT_ME = 'about_me';
    case MARITAL_STATUS = 'marital_status';
    case LANGUAGE = 'language';

    /**
     * Inson tushunadigan o'zbekcha nom.
     */
    public function label(): string
    {
        return match ($this) {
            self::INTEREST => 'Qiziqishlar',
            self::DATING_PURPOSE => 'Tanishishdan Maqsad',
            self::LIFESTYLE => 'Turmush Tarzi',
            self::ABOUT_ME => 'Men Haqimda',
            self::MARITAL_STATUS => 'Oilaviy Holati',
            self::LANGUAGE => 'Biladigan Tillari',
        };
    }

    /**
     * Feather icon nomi.
     */
    public function icon(): string
    {
        return match ($this) {
            self::INTEREST => 'feather-heart',
            self::DATING_PURPOSE => 'feather-target',
            self::LIFESTYLE => 'feather-activity',
            self::ABOUT_ME => 'feather-user',
            self::MARITAL_STATUS => 'feather-users',
            self::LANGUAGE => 'feather-globe',
        };
    }

    /**
     * Badge klassi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::INTEREST => 'bg-soft-danger text-danger',
            self::DATING_PURPOSE => 'bg-soft-primary text-primary',
            self::LIFESTYLE => 'bg-soft-success text-success',
            self::ABOUT_ME => 'bg-soft-info text-info',
            self::MARITAL_STATUS => 'bg-soft-warning text-warning',
            self::LANGUAGE => 'bg-soft-secondary text-dark',
        };
    }
}
