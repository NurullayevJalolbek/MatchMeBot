<?php

namespace Database\Seeders;

use App\Enums\Like\LikeStatusEnum;
use App\Enums\User\GenderEnum;
use App\Models\ModelFile;
use App\Models\User;
use App\Models\UserLike;
use Illuminate\Database\Seeder;

class UserLikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainUser = User::first();
        if (!$mainUser) {
            $mainUser = User::create([
                'telegram_id' => 123456789,
                'first_name' => 'Jasur',
                'name' => 'Jasur',
                'age' => 24,
                'gender' => GenderEnum::MALE->value,
                'looking_for' => GenderEnum::FEMALE->value,
                'city' => 'tashkent_city',
                'onboarding_completed' => true,
                'balance' => 0,
            ]);
        }

        // Demo girls
        $girls = [
            [
                'name' => 'Diyora',
                'age' => 22,
                'city' => 'tashkent_city',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&auto=format&fit=crop&q=80',
                'is_gift' => true,
                'gift_name' => 'Atirgul',
                'gift_icon' => '🌹',
                'subtext' => 'Top-1 Moslik',
            ],
            [
                'name' => 'Sabina',
                'age' => 24,
                'city' => 'samarkand',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&auto=format&fit=crop&q=80',
                'is_gift' => true,
                'gift_name' => 'Oltin Yurak',
                'gift_icon' => '💖',
                'subtext' => 'VIP Match',
            ],
            [
                'name' => 'Aziza',
                'age' => 21,
                'city' => 'tashkent_city',
                'photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&auto=format&fit=crop&q=80',
                'is_gift' => false,
                'gift_name' => null,
                'gift_icon' => null,
                'subtext' => 'Toshkent',
            ],
            [
                'name' => 'Nilufar',
                'age' => 25,
                'city' => 'bukhara',
                'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=600&auto=format&fit=crop&q=80',
                'is_gift' => false,
                'gift_name' => null,
                'gift_icon' => null,
                'subtext' => 'Buxoro',
            ],
            [
                'name' => 'Madina',
                'age' => 23,
                'city' => 'samarkand',
                'photo' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=600&auto=format&fit=crop&q=80',
                'is_gift' => false,
                'gift_name' => null,
                'gift_icon' => null,
                'subtext' => 'Samarqand',
            ],
            [
                'name' => 'Zarina',
                'age' => 20,
                'city' => 'fergana',
                'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=600&auto=format&fit=crop&q=80',
                'is_gift' => false,
                'gift_name' => null,
                'gift_icon' => null,
                'subtext' => "Farg'ona",
            ],
        ];

        foreach ($girls as $index => $g) {
            $girlUser = User::updateOrCreate(
                ['telegram_id' => 990000 + $index],
                [
                    'first_name' => $g['name'],
                    'name' => $g['name'],
                    'age' => $g['age'],
                    'gender' => GenderEnum::FEMALE->value,
                    'looking_for' => GenderEnum::MALE->value,
                    'city' => $g['city'],
                    'bio' => "Salom, men {$g['name']}. MatchMe'da yangiman!",
                    'onboarding_completed' => true,
                ]
            );

            // Add photo
            ModelFile::updateOrCreate(
                [
                    'model_type' => User::class,
                    'model_id' => $girlUser->id,
                    'is_main' => true,
                ],
                [
                    'file_type' => 'photo',
                    'file_path' => $g['photo'],
                    'order' => 1,
                ]
            );

            // Create Like/Gift to mainUser
            UserLike::updateOrCreate(
                [
                    'from_user_id' => $girlUser->id,
                    'to_user_id' => $mainUser->id,
                ],
                [
                    'is_gift' => $g['is_gift'],
                    'gift_name' => $g['gift_name'],
                    'gift_icon' => $g['gift_icon'],
                    'status' => LikeStatusEnum::PENDING->value,
                ]
            );
        }
    }
}
