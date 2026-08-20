<?php

namespace App\Services;

use App\Contracts\iSubscriptionService;
use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionService implements iSubscriptionService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = SubscriptionPlan::class;

    /**
     * Model uchun query builder.
     */
    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Paginate subscription plans for admin management.
     */
    public function paginateSubscriptionPlans(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('incomeCategory')->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Create a new subscription plan.
     */
    public function createSubscriptionPlan(array $data): SubscriptionPlan
    {
        $data = $this->prepareData($data);

        return $this->modelClass::create($data);
    }

    /**
     * Update an existing subscription plan.
     */
    public function updateSubscriptionPlan(SubscriptionPlan $plan, array $data): SubscriptionPlan
    {
        $data = $this->prepareData($data);

        $plan->update($data);

        return $plan;
    }

    /**
     * Delete a subscription plan.
     */
    public function deleteSubscriptionPlan(SubscriptionPlan $plan): bool
    {
        return (bool) $plan->delete();
    }

    /**
     * Toggle subscription plan status between active and inactive.
     */
    public function toggleSubscriptionPlanStatus(SubscriptionPlan $plan): SubscriptionPlan
    {
        $currentStatus = $plan->status instanceof SubscriptionStatusEnum ? $plan->status->value : ($plan->status ?: 'active');
        $newStatus = ($currentStatus === SubscriptionStatusEnum::ACTIVE->value) ? SubscriptionStatusEnum::INACTIVE : SubscriptionStatusEnum::ACTIVE;

        $plan->update([
            'status' => $newStatus,
            'is_active' => $newStatus === SubscriptionStatusEnum::ACTIVE,
        ]);

        return $plan;
    }

    /**
     * Get active plans for user mini-app.
     */
    public function getActivePlans(): Collection
    {
        return $this->query()
            ->active()
            ->get();
    }

    /**
     * Activate a subscription plan for a user.
     */
    public function activateSubscription(User $user, int $planId): array
    {
        $plan = $this->query()->active()->find($planId);

        if (!$plan) {
            return [
                'success' => false,
                'message' => 'Tanlangan obuna rejasi topilmadi yoki faol emas!',
            ];
        }

        $cost = (float) $plan->price;
        $userBalance = (float) ($user->balance ?? 0);

        if ($userBalance < $cost) {
            return [
                'success' => false,
                'message' => "Balansingizda mablag' yetarli emas! Obuna narxi: " . number_format($cost, 0, '.', ' ') . " UZS, sizda: " . number_format($userBalance, 0, '.', ' ') . " UZS",
                'required_amount' => $cost - $userBalance,
            ];
        }

        return DB::transaction(function () use ($user, $plan, $cost) {
            $user->decrement('balance', $cost);

            $currentExpiry = ($user->premium_expires_at && Carbon::parse($user->premium_expires_at)->isFuture())
                ? Carbon::parse($user->premium_expires_at)
                : now();

            $daysToAdd = max(1, $plan->days ?: 30);
            $newExpiry = $currentExpiry->addDays($daysToAdd);

            $user->update([
                'is_premium' => true,
                'premium_expires_at' => $newExpiry,
            ]);

            return [
                'success' => true,
                'message' => '👑 ' . $plan->title . ' muvaffaqiyatli faollashtirildi!',
                'new_balance' => (float) $user->fresh()->balance,
                'formatted_balance' => number_format((float) $user->fresh()->balance, 0, '.', ' ') . ' UZS',
                'premium_expires_at' => $newExpiry->toDateTimeString(),
            ];
        });
    }

    /**
     * Clean and prepare data attributes before persisting.
     */
    protected function prepareData(array $data): array
    {
        $periodCount = (int) ($data['period_count'] ?? 1);
        $periodTypeStr = $data['period_type'] ?? 'month';
        $periodTypeEnum = SubscriptionPeriodTypeEnum::tryFrom($periodTypeStr) ?? SubscriptionPeriodTypeEnum::MONTH;

        // Auto calculate days
        $data['days'] = $periodTypeEnum->toDays($periodCount);

        // Status & is_active
        $statusValue = $data['status'] instanceof SubscriptionStatusEnum ? $data['status']->value : ($data['status'] ?? 'active');
        $data['status'] = $statusValue;
        $data['is_active'] = $statusValue === SubscriptionStatusEnum::ACTIVE->value;
        $data['order'] = $data['order'] ?? 0;
        $data['icon'] = $data['icon'] ?: '👑';

        unset($data['features']);

        return $data;
    }
}
