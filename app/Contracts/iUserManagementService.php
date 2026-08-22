<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface iUserManagementService
{
    /**
     * Get paginated regular users with filters and search.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get user statistics for dashboard widgets.
     */
    public function getUserStatistics(): array;

    /**
     * Find user with all relationships for details page.
     */
    public function getUserDetails(int $id): User;

    /**
     * Toggle active/blocked status.
     */
    public function toggleStatus(int $id): bool;

    /**
     * Grant Subscription / VIP to user based on a SubscriptionPlan.
     */
    public function grantSubscription(int $userId, int $planId): bool;

    /**
     * Revoke Subscription / VIP from user.
     */
    public function revokeSubscription(int $userId): bool;

    /**
     * Grant Boost to user based on a BoostPlan.
     */
    public function grantBoost(int $userId, int $planId): bool;

    /**
     * Revoke Boost from user.
     */
    public function revokeBoost(int $userId): bool;

    /**
     * Delete user safely.
     */
    public function deleteUser(int $id): bool;
}
