<?php

namespace Database\Seeders;

use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\IncomeCategory;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kerakli bola tushum kategoriyalarni topamiz
        $subCat1Week  = IncomeCategory::where('name', '1 haftalik')->first();
        $subCat1Month = IncomeCategory::where('name', '1 oylik')->first();
        $subCat3Month = IncomeCategory::where('name', '3 oylik')->first();

        SubscriptionPlan::query()->delete();

        $plans = [
            [
                'income_category_id' => $subCat1Week?->id,
                'title' => 'MatchMe Premium 1 Haftalik',
                'description' => 'Cheklovlarsiz 7 kunlik to\'liq Premium imkoniyatlar',
                'icon' => '⭐',
                'period_count' => 1,
                'period_type' => SubscriptionPeriodTypeEnum::WEEK->value,
                'days' => 7,
                'price' => 25000,
                'original_price' => null,
                'badge' => null,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'income_category_id' => $subCat1Month?->id,
                'title' => 'MatchMe Premium 1 Oylik',
                'description' => 'Eng mashhur 30 kunlik Premium VIP paket',
                'icon' => '👑',
                'period_count' => 1,
                'period_type' => SubscriptionPeriodTypeEnum::MONTH->value,
                'days' => 30,
                'price' => 79000,
                'original_price' => 100000,
                'badge' => 'MASHHUR 🔥',
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'income_category_id' => $subCat3Month?->id,
                'title' => 'MatchMe Premium 3 Oylik',
                'description' => 'Maksimal foyda va 90 kunlik cheksiz Premium',
                'icon' => '💎',
                'period_count' => 3,
                'period_type' => SubscriptionPeriodTypeEnum::MONTH->value,
                'days' => 90,
                'price' => 199000,
                'original_price' => 240000,
                'badge' => 'SUPER TEJAM 🤑',
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }
    }
}
