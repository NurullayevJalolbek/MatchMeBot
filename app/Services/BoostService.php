<?php

namespace App\Services;

use App\Contracts\iBoostService;
use App\Enums\Boost\BoostStatusEnum;
use App\Models\BoostPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BoostService implements iBoostService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = BoostPlan::class;

    /**
     * Model uchun yangi query builder olish.
     */
    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Get active boost plans from database and user current boost status.
     */
    public function getBoostStatus(User $user): array
    {
        $isBoosted = $user->boost_expires_at && Carbon::parse($user->boost_expires_at)->isFuture();

        $plans = $this->query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->name,
                    'subtitle' => $plan->subtitle,
                    'icon' => $plan->icon,
                    'hours' => $plan->hours,
                    'price' => (float) $plan->price,
                    'formatted_price' => $plan->formatted_price,
                    'original_price' => $plan->formatted_original_price,
                    'badge' => $plan->badge,
                    'badge_type' => $plan->badge_type,
                ];
            })
            ->toArray();

        return [
            'is_boosted' => (bool) $isBoosted,
            'boost_expires_at' => $user->boost_expires_at,
            'plans' => $plans,
        ];
    }

    /**
     * Activate a boost plan using user balance.
     */
    public function activateBoost(User $user, int $planId): array
    {
        $plan = $this->query()->where('is_active', true)->find($planId);

        if (!$plan) {
            return [
                'success' => false,
                'message' => 'Tanlangan Boost rejasi topilmadi yoki faol emas!',
            ];
        }

        $cost = (float) $plan->price;
        $userBalance = (float) ($user->balance ?? 0);

        if ($userBalance < $cost) {
            return [
                'success' => false,
                'message' => "Balansingizda mablag' yetarli emas! Boost narxi: " . number_format($cost, 0, '.', ' ') . " UZS, sizda: " . number_format($userBalance, 0, '.', ' ') . " UZS",
                'required_amount' => $cost - $userBalance,
            ];
        }

        return DB::transaction(function () use ($user, $plan, $cost) {
            $user->decrement('balance', $cost);

            $currentExpiry = ($user->boost_expires_at && Carbon::parse($user->boost_expires_at)->isFuture())
                ? Carbon::parse($user->boost_expires_at)
                : now();

            $newExpiry = $currentExpiry->addHours($plan->hours);

            $user->update(['boost_expires_at' => $newExpiry]);

            return [
                'success' => true,
                'message' => '🚀 ' . $plan->name . ' muvaffaqiyatli faollashtirildi!',
                'new_balance' => (float) $user->fresh()->balance,
                'formatted_balance' => number_format((float) $user->fresh()->balance, 0, '.', ' ') . ' UZS',
                'boost_expires_at' => $newExpiry->toDateTimeString(),
            ];
        });
    }

    /**
     * Paginate boost plans for admin management with filters.
     */
    public function paginateBoostPlans(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('incomeCategory')->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Create a new boost plan.
     */
    public function createBoostPlan(array $data): BoostPlan
    {
        $data['name'] = $data['title'];
        $data['subtitle'] = $data['description'] ?? null;
        $data['icon'] = $data['icon'] ?? '⚡';
        $statusValue = $data['status'] instanceof BoostStatusEnum ? $data['status']->value : $data['status'];
        $data['is_active'] = $statusValue === BoostStatusEnum::ACTIVE->value;
        $data['order'] = $data['order'] ?? 0;

        return $this->modelClass::create($data);
    }

    /**
     * Update an existing boost plan.
     */
    public function updateBoostPlan(BoostPlan $boost, array $data): BoostPlan
    {
        $data['name'] = $data['title'];
        $data['subtitle'] = $data['description'] ?? null;
        $data['icon'] = $data['icon'] ?? '⚡';
        $statusValue = $data['status'] instanceof BoostStatusEnum ? $data['status']->value : $data['status'];
        $data['is_active'] = $statusValue === BoostStatusEnum::ACTIVE->value;
        $data['order'] = $data['order'] ?? 0;

        $boost->update($data);

        return $boost;
    }

    /**
     * Delete a boost plan.
     */
    public function deleteBoostPlan(BoostPlan $boost): bool
    {
        return (bool) $boost->delete();
    }

    /**
     * Toggle boost plan status between active and inactive.
     */
    public function toggleBoostPlanStatus(BoostPlan $boost): BoostPlan
    {
        $currentStatus = $boost->status instanceof BoostStatusEnum ? $boost->status->value : ($boost->status ?: 'active');
        $newStatus = ($currentStatus === BoostStatusEnum::ACTIVE->value) ? BoostStatusEnum::INACTIVE : BoostStatusEnum::ACTIVE;

        $boost->update([
            'status' => $newStatus,
            'is_active' => $newStatus === BoostStatusEnum::ACTIVE,
        ]);

        return $boost;
    }
}
