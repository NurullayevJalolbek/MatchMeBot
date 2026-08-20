<?php

namespace Database\Seeders;

use App\Enums\Boost\BoostStatusEnum;
use App\Models\BoostPlan;
use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class BoostPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kerakli bola kategoriyalarni topamiz
        $boostCat1Hour = IncomeCategory::where('name', '1 soatlik')->first();
        $boostCat3Hour = IncomeCategory::where('name', '3 soatlik')->first();
        $boostCat1Day  = IncomeCategory::where('name', '1 kunlik')->first();

        BoostPlan::query()->delete();

        $plans = [
            [
                'income_category_id' => $boostCat1Hour?->id,
                'title' => '1 soatlik Boost',
                'name' => '1 soatlik Boost',
                'description' => '1 soat davomida barcha qidiruvlarda TOP-1 bo\'lasiz',
                'subtitle' => '1 soat davomida barcha qidiruvlarda TOP-1',
                'icon' => '⚡',
                'hours' => 1,
                'price' => 10000,
                'original_price' => null,
                'badge' => null,
                'badge_type' => 'popular',
                'status' => BoostStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'income_category_id' => $boostCat3Hour?->id,
                'title' => '3 soatlik Boost',
                'name' => '3 soatlik Boost',
                'description' => '3 soat TOP-1 • 33% tejash va eng ko\'p layklar',
                'subtitle' => '3 soat TOP-1 • 33% tejash',
                'icon' => '🚀',
                'hours' => 3,
                'price' => 25000,
                'original_price' => 30000,
                'badge' => 'MASHHUR 🔥',
                'badge_type' => 'popular',
                'status' => BoostStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'income_category_id' => $boostCat1Day?->id,
                'title' => '1 kunlik Boost',
                'name' => '1 kunlik Boost',
                'description' => '24 soat mutlaq TOP-1 • Maksimal e\'tibor va yangi tanishuvlar',
                'subtitle' => '24 soat mutlaq TOP-1 • Katta chegirma',
                'icon' => '🌟',
                'hours' => 24,
                'price' => 50000,
                'original_price' => 80000,
                'badge' => 'SUPER TEJAM 🤑',
                'badge_type' => 'super',
                'status' => BoostStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            BoostPlan::create($plan);
        }
    }
}
