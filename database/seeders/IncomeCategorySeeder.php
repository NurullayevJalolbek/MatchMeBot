<?php

namespace Database\Seeders;

use App\Enums\Finance\FinanceStatusEnum;
use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tozalash
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        IncomeCategory::query()->delete();

        // 1. Premium obunalar (Ota kategoriya)
        $premiumParent = IncomeCategory::create([
            'name' => 'Premium obunalar',
            'icon' => '💎',
            'description' => 'MatchMe Premium VIP obunalar sotuvidan barcha tushumlar',
            'status' => FinanceStatusEnum::ACTIVE->value,
            'is_active' => true,
            'order' => 1,
            'parent_id' => null,
        ]);

        // Premium obunalar bolalari: 1 haftalik, 1 oylik, 3 oylik
        $subChildren = [
            [
                'name' => '1 haftalik',
                'icon' => '⭐',
                'description' => '1 haftalik (7 kunlik) Premium obuna tushumlari',
                'order' => 1,
            ],
            [
                'name' => '1 oylik',
                'icon' => '👑',
                'description' => '1 oylik (30 kunlik) Premium obuna tushumlari',
                'order' => 2,
            ],
            [
                'name' => '3 oylik',
                'icon' => '💎',
                'description' => '3 oylik (90 kunlik) Premium obuna tushumlari',
                'order' => 3,
            ],
        ];

        foreach ($subChildren as $child) {
            $child['parent_id'] = $premiumParent->id;
            $child['status'] = FinanceStatusEnum::ACTIVE->value;
            $child['is_active'] = true;
            IncomeCategory::create($child);
        }

        // 2. Boost (Ota kategoriya)
        $boostParent = IncomeCategory::create([
            'name' => 'Boost',
            'icon' => '⚡',
            'description' => 'Anketani TOP-1 ga ko\'tarish xizmati tushumlari',
            'status' => FinanceStatusEnum::ACTIVE->value,
            'is_active' => true,
            'order' => 2,
            'parent_id' => null,
        ]);

        // Boost bolalari: 1 soatlik, 3 soatlik, 1 kunlik
        $boostChildren = [
            [
                'name' => '1 soatlik',
                'icon' => '⚡',
                'description' => '1 soatlik tezkor boost tushumlari',
                'order' => 1,
            ],
            [
                'name' => '3 soatlik',
                'icon' => '🚀',
                'description' => '3 soatlik faol boost tushumlari',
                'order' => 2,
            ],
            [
                'name' => '1 kunlik',
                'icon' => '🌟',
                'description' => '1 kunlik (24 soat) VIP boost tushumlari',
                'order' => 3,
            ],
        ];

        foreach ($boostChildren as $child) {
            $child['parent_id'] = $boostParent->id;
            $child['status'] = FinanceStatusEnum::ACTIVE->value;
            $child['is_active'] = true;
            IncomeCategory::create($child);
        }
    }
}
