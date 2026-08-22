<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface iProfileService
{
    /**
     * Get complete profile data and statistics for Mini-App profile screen.
     */
    public function getProfile(User $user): array;

    /**
     * Get all reference options, regions, districts and user's current selections for Edit Profile screen.
     */
    public function getEditProfileData(User $user): array;

    /**
     * Update user profile information with all sections (bio, details, options, regions).
     */
    public function updateProfile(User $user, array $data): User;

    /**
     * Link Instagram username to profile.
     */
    public function linkInstagram(User $user, string $instagramUsername): User;

    /**
     * Upload a profile photo (max 3 allowed).
     */
    public function uploadPhoto(User $user, UploadedFile $file, bool $isMain = false, ?int $replacePhotoId = null): array;

    /**
     * Delete a profile photo.
     */
    public function deletePhoto(User $user, int $photoId): bool;

    /**
     * Set a photo as primary/main.
     */
    public function setPrimaryPhoto(User $user, int $photoId): bool;

    /**
     * Get user expenses & transaction history.
     */
    public function getExpensesHistory(User $user): array;

    /**
     * Calculate and sync user's profile completion percentage.
     */
    public function calculateCompletion(User $user): array;
}
