<?php

namespace App\Contracts;

use App\Models\User;

interface iTelegramUserService
{
    /**
     * Get or create a Telegram user from incoming update data.
     */
    public function getOrCreateUser(array $fromData): User;

    /**
     * Update user language preference.
     */
    public function updateLanguage(int|string $telegramId, string $languageCode): User;

    /**
     * Get Telegram user by Telegram ID.
     */
    public function getByTelegramId(int|string $telegramId): ?User;
}
