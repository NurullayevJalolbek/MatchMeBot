<?php

namespace Database\Seeders;

use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultFeatures = [
            [
                'icon' => '❤️',
                'title' => 'Cheksiz Layklar (Unlimited Likes)',
                'description' => 'Kunlik layk cheklovisiz istagancha profillarni yoqtiring va tanishing.',
            ],
            [
                'icon' => '👁️',
                'title' => 'Sizga kim layk bosganini ko\'rish',
                'description' => 'Profilingizni yoqtirgan barcha insonlarni darhol ochib ko\'ring va xabar yozing.',
            ],
            [
                'icon' => '🔄',
                'title' => 'Anketalarni qaytarish (Rewind)',
                'description' => 'Bilmasdan yoki shoshilib o\'tkazib yuborgan anketalarni darhol orqaga qaytaring.',
            ],
            [
                'icon' => '✈️',
                'title' => 'Telegram Username ko\'rish',
                'description' => 'Foydalanuvchining shaxsiy Telegram username\'ini to\'g\'ridan-to\'g\'ri ko\'ring (agar bor bo\'lsa).',
            ],
            [
                'icon' => '⭐',
                'title' => 'TOP-1 Profil Ko\'rsatilish',
                'description' => 'Qidiruvlarda doimiy ravishda birinchilar qatorida tavsiya etilish.',
            ],
        ];

        $plans = [
            [
                'title' => 'MatchMe Premium 7 Kunlik',
                'description' => 'Cheklovlarsiz 7 kunlik to\'liq Premium imkoniyatlar',
                'icon' => '⭐',
                'period_count' => 7,
                'period_type' => SubscriptionPeriodTypeEnum::DAY->value,
                'days' => 7,
                'price' => 20000,
                'original_price' => null,
                'badge' => null,
                'features' => $defaultFeatures,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'MatchMe Premium 1 Oylik',
                'description' => 'Eng mashhur 1 oylik to\'liq VIP obuna paketi',
                'icon' => '👑',
                'period_count' => 1,
                'period_type' => SubscriptionPeriodTypeEnum::MONTH->value,
                'days' => 30,
                'price' => 30000,
                'original_price' => 45000,
                'badge' => 'MASHHUR 🔥',
                'features' => $defaultFeatures,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'MatchMe Premium 3 Oylik',
                'description' => '3 oylik maksimal tejovchi Premium obuna paketi',
                'icon' => '💎',
                'period_count' => 3,
                'period_type' => SubscriptionPeriodTypeEnum::MONTH->value,
                'days' => 90,
                'price' => 60000,
                'original_price' => 90000,
                'badge' => '-50% TEJAM 🤑',
                'features' => $defaultFeatures,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['title' => $plan['title']],
                $plan
            );
        }
    }
}
