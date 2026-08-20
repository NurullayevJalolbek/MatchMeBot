<?php

namespace Database\Seeders;

use App\Enums\Finance\FinanceStatusEnum;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Marketing & Reklama',
                'icon' => '📢',
                'description' => 'Barcha turdagi reklama va marketing xarajatlari',
                'order' => 1,
                'children' => [
                    ['name' => 'Telegram Reklama', 'icon' => '✈️', 'description' => 'Telegram kanallar va botlarda reklama'],
                    ['name' => 'Instagram & Meta Ads', 'icon' => '📸', 'description' => 'Target reklama xarajatlari'],
                    ['name' => 'Bloggerlar & Influencerlar', 'icon' => '⭐', 'description' => 'Shaxsiy bloggerlar orqali integratsiyalar'],
                    ['name' => 'Google & Yandex Ads', 'icon' => '🔍', 'description' => 'Qidiruv tizimlaridagi kontekst reklama'],
                ],
            ],
            [
                'name' => 'IT & Server Infratuzilmasi',
                'icon' => '💻',
                'description' => 'Serverlar, hosting, domenlar va API xizmatlari',
                'order' => 2,
                'children' => [
                    ['name' => 'Server & Cloud (VPS)', 'icon' => '☁️', 'description' => 'Server ijarasi va bulutli xizmatlar'],
                    ['name' => 'Domen & SSL Sertifikatlar', 'icon' => '🌐', 'description' => 'Domen nomlari va xavfsizlik sertifikatlari'],
                    ['name' => 'SMS Gateway Xizmatlari', 'icon' => '📱', 'description' => 'SMS tasdiqlash provayderlari to\'lovlari'],
                ],
            ],
            [
                'name' => 'Ish Haqi & Bonuslar',
                'icon' => '👥',
                'description' => 'Jamoa a\'zolari va mutaxassislar uchun to\'lovlar',
                'order' => 3,
                'children' => [
                    ['name' => 'Dasturchilar Maoshi', 'icon' => '👨‍💻', 'description' => 'Backend, Frontend va mobil dasturchilar'],
                    ['name' => 'Operator & Moderatorlar', 'icon' => '🎧', 'description' => 'Mijozlarni qo\'llab-quvvatlash jamoasi'],
                    ['name' => 'Dizayn & SMM Mutaxassislar', 'icon' => '🎨', 'description' => 'Kontent va vizual dizayn yaratuvchilar'],
                ],
            ],
            [
                'name' => 'Ofis & Ma\'muriy Xarajatlar',
                'icon' => '🏢',
                'description' => 'Ofis ijarasi, kommunal va boshqa ma\'muriy xarajatlar',
                'order' => 4,
                'children' => [
                    ['name' => 'Ofis Ijarasi', 'icon' => '🔑', 'description' => 'Oylik ofis ijara haqi'],
                    ['name' => 'Kommunal & Internet', 'icon' => '⚡', 'description' => 'Elektr, suv, isitish va yuqori tezlikdagi internet'],
                    ['name' => 'Kantselyariya & Xo\'jalik', 'icon' => '☕', 'description' => 'Ofis jihozlari, qog\'oz, choy/kofe va boshqalar'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $catData['status'] = FinanceStatusEnum::ACTIVE->value;
            $catData['is_active'] = true;

            /** @var ExpenseCategory $parent */
            $parent = ExpenseCategory::create($catData);

            $childOrder = 1;
            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['order'] = $childOrder++;
                $childData['status'] = FinanceStatusEnum::ACTIVE->value;
                $childData['is_active'] = true;

                ExpenseCategory::create($childData);
            }
        }
    }
}
