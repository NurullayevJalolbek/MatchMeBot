<?php

namespace Database\Seeders;

use App\Enums\User\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'code' => RoleEnum::ADMIN->value,
                'status' => 'active',
                'description' => 'Tizimning to\'liq boshqaruv huquqiga ega admin',
            ],
            [
                'name' => 'Foydalanuvchi',
                'code' => RoleEnum::USER->value,
                'status' => 'active',
                'description' => 'Oddiy ro\'yxatdan o\'tgan bot/ilova foydalanuvchisi',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );
        }
    }
}
