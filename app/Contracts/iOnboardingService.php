<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface iOnboardingService
{
    /**
     * Authenticate or get user from Telegram WebApp initData or session.
     */
    public function getOrCreateTelegramUser(array $telegramData): User;

    /**
     * Accept Terms of Service.
     */
    public function acceptTerms(User $user): User;

    /**
     * Save specific onboarding step data.
     */
    public function saveStep(User $user, int $step, array $data): User;

    /**
     * Upload user profile photo and return stored URL.
     */
    public function uploadPhoto(User $user, UploadedFile $file): string;

    /**
     * Remove photo by index.
     */
    public function deletePhoto(User $user, int $photoIndex): User;

    /**
     * Complete onboarding wizard.
     */
    public function completeOnboarding(User $user): User;
}
