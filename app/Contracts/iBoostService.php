<?php

namespace App\Contracts;

use App\Models\BoostPlan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    /**
     * Paginate boost plans for admin management with filters.
     */
    public function paginateBoostPlans(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new boost plan.
     */
    public function createBoostPlan(array $data): BoostPlan;

    /**
     * Update an existing boost plan.
     */
    public function updateBoostPlan(BoostPlan $boost, array $data): BoostPlan;

    /**
     * Delete a boost plan.
     */
    public function deleteBoostPlan(BoostPlan $boost): bool;

    /**
     * Toggle boost plan status between active and inactive.
     */
    public function toggleBoostPlanStatus(BoostPlan $boost): BoostPlan;
}
