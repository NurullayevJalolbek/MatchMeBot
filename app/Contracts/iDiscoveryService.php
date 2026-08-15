<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\UserFilter;

interface iDiscoveryService
{
    /**
     * Get or create discovery filter preferences for user.
     */
    public function getFilter(User $user): UserFilter;

    /**
     * Update user discovery filter preferences.
     */
    public function saveFilter(User $user, array $filterData): UserFilter;

    /**
     * Fetch matching candidate cards based on user filters.
     */
    public function getCandidates(User $user, int $limit = 20): array;
}
