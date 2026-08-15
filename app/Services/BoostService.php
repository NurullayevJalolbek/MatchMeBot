<?php

namespace App\Services;

use App\Contracts\iBoostService;
use App\Models\BoostPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BoostService implements iBoostService
{
    /**
     * Get active boost plans from database and user current boost status.
     */
    public function getBoostStatus(User $user): array
    {
        $isBoosted = $user->boost_expires_at && Carbon::parse($user->boost_expires_at)->isFuture();

        $plans = BoostPlan::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function (BoostPlan $plan) {
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
            'balance' => (float) ($user->balance ?? 0),
            'formatted_balance' => number_format((float) ($user->balance ?? 0), 0, '.', ' ') . ' UZS',
            'plans' => $plans,
        ];
    }

    /**
     * Activate a boost plan using user balance.
     */
    public function activateBoost(User $user, int $planId): array
    {
        $plan = BoostPlan::where('is_active', true)->find($planId);

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
}
