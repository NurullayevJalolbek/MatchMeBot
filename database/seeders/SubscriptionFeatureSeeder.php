<?php

namespace Database\Seeders;

use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\SubscriptionFeature;
use Illuminate\Database\Seeder;

class SubscriptionFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'title' => 'Cheksiz Layklar (Unlimited Likes)',
                'description' => 'Kunlik layk cheklovisiz istagancha profillarni yoqtiring va tanishing.',
                'icon' => null,
                'order' => 1,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
            ],
            [
                'title' => 'Sizga kim layk bosganini ko\'rish',
                'description' => 'Profilingizni yoqtirgan barcha insonlarni darhol ochib ko\'ring va xabar yozing.',
                'icon' => null,
                'order' => 2,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
            ],
            [
                'title' => 'Anketalarni qaytarish (Rewind)',
                'description' => 'Bilmasdan yoki shoshilib o\'tkazib yuborgan anketalarni darhol orqaga qaytaring.',
                'icon' => null,
                'order' => 3,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
            ],
            [
                'title' => 'Telegram Username ko\'rish',
                'description' => 'Foydalanuvchining shaxsiy Telegram username\'ini to\'g\'ridan-to\'g\'ri ko\'ring (agar bor bo\'lsa).',
                'icon' => null,
                'order' => 4,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
            ],
            [
                'title' => 'TOP-1 Profil Ko\'rsatilish',
                'description' => 'Qidiruvlarda doimiy ravishda birinchilar qatorida tavsiya etilish.',
                'icon' => null,
                'order' => 5,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            SubscriptionFeature::updateOrCreate(
                ['title' => $feature['title']],
                $feature
            );
        }
    }
}
