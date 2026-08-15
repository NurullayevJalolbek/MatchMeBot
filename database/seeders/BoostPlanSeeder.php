<?php

namespace Database\Seeders;

use App\Models\BoostPlan;
use Illuminate\Database\Seeder;

class BoostPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => '1 soatlik Boost',
                'subtitle' => '1 soat davomida barcha qidiruvlarda TOP-1',
                'icon' => '⚡',
                'hours' => 1,
                'price' => 10000,
                'original_price' => null,
                'badge' => null,
                'badge_type' => 'popular',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => '3 soatlik Boost',
                'subtitle' => '3 soat TOP-1 • 33% tejash',
                'icon' => '🚀',
                'hours' => 3,
                'price' => 20000,
                'original_price' => 30000,
                'badge' => 'MASHHUR 🔥',
                'badge_type' => 'popular',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => '10 soatlik Super Boost',
                'subtitle' => '10 soat mutlaq TOP-1 • 55% tejash',
                'icon' => '👑',
                'hours' => 10,
                'price' => 45000,
                'original_price' => 100000,
                'badge' => 'SUPER TEJAM 🤑',
                'badge_type' => 'super',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            BoostPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
