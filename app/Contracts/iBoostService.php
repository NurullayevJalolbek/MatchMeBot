<?php

namespace App\Contracts;

use App\Models\BoostPlan;
use App\Models\User;

interface iBoostService
{
    /**
     * Get active boost plans from database and user current boost status.
     */
    public function getBoostStatus(User $user): array;

    /**
     * Activate a boost plan using user balance.
     */
    public function activateBoost(User $user, int $planId): array;
}
