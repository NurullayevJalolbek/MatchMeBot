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
        // 1. Create or update Default Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@matchme.uz'],
            [
                'name' => 'Admin Administrator',
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'gender' => GenderEnum::MALE->value,
                'city' => 'tashkent',
                'age' => 25,
                'balance' => 100000,
                'onboarding_completed' => true,
            ]
        );

        // Assign Admin role
        $adminRole = Role::where('code', RoleEnum::ADMIN->value)->first();
        if ($adminRole && !$adminUser->roles->contains($adminRole->id)) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // 2. Also ensure other users have 'user' role
        $userRole = Role::where('code', RoleEnum::USER->value)->first();
        if ($userRole) {
            $otherUsers = User::where('id', '!=', $adminUser->id)->get();
            foreach ($otherUsers as $user) {
                if (!$user->roles->contains($userRole->id)) {
                    $user->roles()->syncWithoutDetaching([$userRole->id]);
                }
            }
        }
    }
}
