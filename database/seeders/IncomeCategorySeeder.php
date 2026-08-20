<?php

namespace Database\Seeders;

use App\Enums\Finance\FinanceStatusEnum;
use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'MatchMe Premium Obunalar',
                'icon' => '💎',
                'description' => 'Foydalanuvchilarning pullik VIP obuna xaridlaridan tushumlar',
                'order' => 1,
                'children' => [
                    ['name' => '7 Kunlik VIP Paket', 'icon' => '⭐', 'description' => '1 haftalik qisqa muddatli VIP obuna'],
                    ['name' => '1 Oylik VIP Paket', 'icon' => '👑', 'description' => 'Eng mashhur 30 kunlik Premium obuna'],
                    ['name' => '3 Oylik VIP Paket', 'icon' => '💎', 'description' => '90 kunlik maksimal tejamkor VIP paket'],
                ],
            ],
            [
                'name' => 'Boost Xizmatlari',
                'icon' => '⚡',
                'description' => 'Anketani qidiruv va tanishuvda birinchi o\'ringa ko\'tarish xizmati',
                'order' => 2,
                'children' => [
                    ['name' => 'Super Boost (1 Soat)', 'icon' => '🚀', 'description' => '1 soatlik tezkor ko\'tarish'],
                    ['name' => 'Super Boost (3 Soat)', 'icon' => '🔥', 'description' => '3 soatlik faol ko\'tarish'],
                    ['name' => 'Super Boost (24 Soat)', 'icon' => '🌟', 'description' => 'Kunlik VIP ko\'tarish'],
                ],
            ],
            [
                'name' => 'Foydalanuvchilar Balans To\'ldirishi',
                'icon' => '💳',
                'description' => 'To\'lov tizimlari orqali hamyon balansini to\'ldirishlar',
                'order' => 3,
                'children' => [
                    ['name' => 'Payme orqali to\'lovlar', 'icon' => '🟢', 'description' => 'Payme integratsiyasi orqali tushum'],
                    ['name' => 'Click orqali to\'lovlar', 'icon' => '🔵', 'description' => 'Click integratsiyasi orqali tushum'],
                    ['name' => 'Uzum Pay orqali to\'lovlar', 'icon' => '🟣', 'description' => 'Uzum to\'lov tizimi orqali tushum'],
                ],
            ],
            [
                'name' => 'Sovg\'alar va Tangalar Xaridi',
                'icon' => '🎁',
                'description' => 'Virtual tangalar va chatdagi pullik sovg\'alar sotuvidan tushum',
                'order' => 4,
                'children' => [
                    ['name' => 'Virtual Tangalar Xaridi', 'icon' => '🪙', 'description' => 'Ilova ichidagi tangalarni sotib olish'],
                    ['name' => 'VIP Sovg\'alar Yuborish', 'icon' => '🌹', 'description' => 'Chatdagi maxsus animatsiyali sovg\'alar'],
                ],
            ],
            [
                'name' => 'Reklama va Hamkorlik Daromadlari',
                'icon' => '📢',
                'description' => 'Mini-App va botdagi reklama integratsiyalaridan tushum',
                'order' => 5,
                'children' => [
                    ['name' => 'Mini-App Banner Reklama', 'icon' => '📱', 'description' => 'Ilova ichidagi reklama bloklari'],
                    ['name' => 'Hamkorlik Integratsiyalari', 'icon' => '🤝', 'description' => 'Boshqa brendlar bilan hamkorlik'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $catData['status'] = FinanceStatusEnum::ACTIVE->value;
            $catData['is_active'] = true;

            /** @var IncomeCategory $parent */
            $parent = IncomeCategory::create($catData);

            $childOrder = 1;
            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['order'] = $childOrder++;
                $childData['status'] = FinanceStatusEnum::ACTIVE->value;
                $childData['is_active'] = true;

                IncomeCategory::create($childData);
            }
        }
    }
}
