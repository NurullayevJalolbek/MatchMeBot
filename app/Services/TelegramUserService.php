<?php

namespace App\Services;

use App\Contracts\iTelegramUserService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TelegramUserService implements iTelegramUserService
{
    /**
     * Get or create a Telegram user from incoming update data.
     */
    public function getOrCreateUser(array $fromData): User
    {
        $telegramId = $fromData['id'] ?? 0;
        $firstName = $fromData['first_name'] ?? null;
        $lastName = $fromData['last_name'] ?? null;
        $fullName = trim("{$firstName} {$lastName}");

        return User::updateOrCreate(
            ['telegram_id' => $telegramId],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $fromData['username'] ?? null,
                'name' => !empty($fullName) ? $fullName : ($fromData['username'] ?? 'User'),
            ]
        );
    }

    /**
     * Update user language preference.
     */
    public function updateLanguage(int|string $telegramId, string $languageCode): User
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if ($user) {
            $user->update(['language_code' => $languageCode]);
            return $user;
        }

        return User::create([
            'telegram_id' => $telegramId,
            'language_code' => $languageCode,
        ]);
    }

    /**
     * Get Telegram user by Telegram ID.
     */
    public function getByTelegramId(int|string $telegramId): ?User
    {
        return User::where('telegram_id', $telegramId)->first();
    }
}
