<?php

namespace Database\Seeders;

use App\Enums\User\GenderEnum;
use App\Enums\User\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Faqat bitta Administrator yaratiladi (oddiy role=user'lar seederda bo'lmaydi)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@matchme.uz'],
            [
                'name' => 'Admin Administrator',
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'gender' => GenderEnum::MALE->value,
                'city' => 'tashkent_city',
                'age' => 25,
                'onboarding_completed' => true,
                'status' => 'active',
            ]
        );

        $adminRole = Role::where('code', RoleEnum::ADMIN->value)->first();
        if ($adminRole && !$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
