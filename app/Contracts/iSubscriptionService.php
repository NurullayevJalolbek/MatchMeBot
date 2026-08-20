<?php

namespace App\Contracts;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface iSubscriptionService
{
    /**
     * Paginate subscription plans for admin management.
     */
    public function paginateSubscriptionPlans(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new subscription plan.
     */
    public function createSubscriptionPlan(array $data): SubscriptionPlan;

    /**
     * Update an existing subscription plan.
     */
    public function updateSubscriptionPlan(SubscriptionPlan $plan, array $data): SubscriptionPlan;

    /**
     * Delete a subscription plan.
     */
    public function deleteSubscriptionPlan(SubscriptionPlan $plan): bool;

    /**
     * Toggle subscription plan status between active and inactive.
     */
    public function toggleSubscriptionPlanStatus(SubscriptionPlan $plan): SubscriptionPlan;

    /**
     * Get active plans for user mini-app.
     */
    public function getActivePlans(): Collection;

    /**
     * Activate a subscription plan for a user.
     */
    public function activateSubscription(User $user, int $planId): array;
}
