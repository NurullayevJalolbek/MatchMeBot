<?php

namespace App\Enums\Subscription;

enum SubscriptionStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get user-friendly human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::INACTIVE => 'Nofaol',
        };
    }
}
