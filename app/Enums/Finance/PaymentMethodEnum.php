<?php

namespace App\Enums\Finance;

enum PaymentMethodEnum: string
{
    case CARD = 'card';
    case CASH = 'cash';
    case BANK = 'bank';
    case OTHER = 'other';

    /**
     * Inson tushunadigan o'zbekcha nom.
     */
    public function label(): string
    {
        return match ($this) {
            self::CARD => 'Karta',
            self::CASH => 'Naqd pul',
            self::BANK => 'Bank orqali',
            self::OTHER => 'Boshqa',
        };
    }

    /**
     * Ikonka nomi.
     */
    public function icon(): string
    {
        return match ($this) {
            self::CARD => 'feather-credit-card',
            self::CASH => 'feather-dollar-sign',
            self::BANK => 'feather-briefcase',
            self::OTHER => 'feather-more-horizontal',
        };
    }
}
