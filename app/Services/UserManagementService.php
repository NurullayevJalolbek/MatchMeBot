<?php

namespace App\Services;

use App\Contracts\iUserManagementService;
use App\Enums\Admin\AdminStatusEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserManagementService implements iUserManagementService
{
    /**
     * Get paginated regular users with filters and search.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::regularUsers()->with(['photos', 'subscriptions', 'boosts']);

        // 1. Matnli Qidiruv (Ism, Familiya, Username, Telegram ID)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('username', 'ILIKE', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('telegram_id', (int) $search);
                }
            });
        }

        // 2. Jinsi
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // 3. Shahar
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        // 4. Holati (Faol / Bloklangan)
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // 5. VIP Holati
        if (isset($filters['is_vip']) && $filters['is_vip'] !== '') {
            if ($filters['is_vip'] == '1') {
                $query->where('is_vip', true)
                      ->where(function ($q) {
                          $q->whereNull('vip_expires_at')
                            ->orWhere('vip_expires_at', '>', Carbon::now());
                      });
            } else {
                $query->where(function ($q) {
                    $q->where('is_vip', false)
                      ->orWhereNull('is_vip')
                      ->orWhere('vip_expires_at', '<=', Carbon::now());
                });
            }
        }

        // 6. Anketa To'ldirilganligi
        if (isset($filters['onboarding_completed']) && $filters['onboarding_completed'] !== '') {
            $query->where('onboarding_completed', (bool) $filters['onboarding_completed']);
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Get user statistics for dashboard widgets.
     */
    public function getUserStatistics(): array
    {
        $now = Carbon::now();

        $totalUsers = User::regularUsers()->count();
        
        $vipUsers = User::regularUsers()
            ->where('is_vip', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('vip_expires_at')
                  ->orWhere('vip_expires_at', '>', $now);
            })->count();

        $boostUsers = User::regularUsers()
            ->where('boost_expires_at', '>', $now)
            ->count();

        $newThisWeek = User::regularUsers()
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->count();

        return [
            'total_users' => $totalUsers,
            'vip_users' => $vipUsers,
            'boost_users' => $boostUsers,
            'new_this_week' => $newThisWeek,
        ];
    }

    /**
     * Find user with all relationships for details page.
     */
    public function getUserDetails(int $id): User
    {
        return User::regularUsers()
            ->with([
                'photos',
                'subscriptions.plan',
                'boosts.plan',
                'payments.incomeCategory',
                'receivedLikes.fromUser',
                'sentLikes.toUser',
            ])
            ->findOrFail($id);
    }

    /**
     * Toggle active/blocked status.
     */
    public function toggleStatus(int $id): bool
    {
        $user = User::regularUsers()->findOrFail($id);

        $newStatus = ($user->status === AdminStatusEnum::ACTIVE)
            ? AdminStatusEnum::INACTIVE
            : AdminStatusEnum::ACTIVE;

        return $user->update(['status' => $newStatus]);
    }

    /**
     * Grant Subscription / VIP to user based on database SubscriptionPlan.
     */
    public function grantSubscription(int $userId, int $planId): bool
    {
        $user = User::regularUsers()->findOrFail($userId);
        $plan = \App\Models\SubscriptionPlan::findOrFail($planId);

        $days = (int) ($plan->days ?: 30);
        $expiresAt = Carbon::now()->addDays($days);

        // Deactivate previous active subscriptions
        \App\Models\UserSubscription::where('user_id', $user->id)
            ->where('status', \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE)
            ->update([
                'status' => \App\Enums\Subscription\UserServiceStatusEnum::EXPIRED,
                'is_active' => false,
            ]);

        // Create new UserSubscription record
        \App\Models\UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::now(),
            'ends_at' => $expiresAt,
            'status' => \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE,
            'is_active' => true,
        ]);

        return $user->update([
            'is_vip' => true,
            'vip_expires_at' => $expiresAt,
        ]);
    }

    /**
     * Revoke Subscription / VIP from user.
     */
    public function revokeSubscription(int $userId): bool
    {
        $user = User::regularUsers()->findOrFail($userId);

        \App\Models\UserSubscription::where('user_id', $user->id)
            ->where('status', \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE)
            ->update([
                'status' => \App\Enums\Subscription\UserServiceStatusEnum::CANCELLED,
                'is_active' => false,
            ]);

        return $user->update([
            'is_vip' => false,
            'vip_expires_at' => null,
        ]);
    }

    /**
     * Grant Boost to user based on database BoostPlan.
     */
    public function grantBoost(int $userId, int $planId): bool
    {
        $user = User::regularUsers()->findOrFail($userId);
        $plan = \App\Models\BoostPlan::findOrFail($planId);

        $hours = (int) ($plan->hours ?: 24);
        $expiresAt = Carbon::now()->addHours($hours);

        // Deactivate previous active boosts
        \App\Models\UserBoost::where('user_id', $user->id)
            ->where('status', \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE)
            ->update([
                'status' => \App\Enums\Subscription\UserServiceStatusEnum::EXPIRED,
                'is_active' => false,
            ]);

        // Create new UserBoost record
        \App\Models\UserBoost::create([
            'user_id' => $user->id,
            'boost_plan_id' => $plan->id,
            'starts_at' => Carbon::now(),
            'ends_at' => $expiresAt,
            'status' => \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE,
            'is_active' => true,
        ]);

        return $user->update([
            'boost_expires_at' => $expiresAt,
        ]);
    }

    /**
     * Revoke Boost from user.
     */
    public function revokeBoost(int $userId): bool
    {
        $user = User::regularUsers()->findOrFail($userId);

        \App\Models\UserBoost::where('user_id', $user->id)
            ->where('status', \App\Enums\Subscription\UserServiceStatusEnum::ACTIVE)
            ->update([
                'status' => \App\Enums\Subscription\UserServiceStatusEnum::CANCELLED,
                'is_active' => false,
            ]);

        return $user->update([
            'boost_expires_at' => null,
        ]);
    }

    /**
     * Delete user safely.
     */
    public function deleteUser(int $id): bool
    {
        $user = User::regularUsers()->findOrFail($id);
        return (bool) $user->delete();
    }
}
