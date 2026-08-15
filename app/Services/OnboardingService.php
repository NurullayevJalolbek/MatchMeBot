<?php

namespace App\Services;

use App\Contracts\iOnboardingService;
use App\Models\ModelFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OnboardingService implements iOnboardingService
{
    /**
     * Authenticate or get user from Telegram WebApp initData or session.
     */
    public function getOrCreateTelegramUser(array $telegramData): User
    {
        $telegramId = $telegramData['id'] ?? null;

        if (!$telegramId) {
            return User::firstOrCreate(
                ['username' => 'guest_demo'],
                [
                    'name' => 'Demo User',
                    'language_code' => 'uz',
                ]
            );
        }

        $firstName = $telegramData['first_name'] ?? null;
        $lastName = $telegramData['last_name'] ?? null;
        $fullName = trim("{$firstName} {$lastName}");

        $user = User::where('telegram_id', $telegramId)->first();

        if ($user) {
            $user->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $telegramData['username'] ?? $user->username,
                'language_code' => $telegramData['language_code'] ?? $user->language_code,
            ]);
            return $user;
        }

        return User::create([
            'telegram_id' => $telegramId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $telegramData['username'] ?? null,
            'language_code' => $telegramData['language_code'] ?? 'uz',
            'name' => !empty($fullName) ? $fullName : ($telegramData['username'] ?? 'User'),
        ]);
    }

    /**
     * Accept Terms of Service.
     */
    public function acceptTerms(User $user): User
    {
        $user->update([
            'is_terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        return $user->fresh();
    }

    /**
     * Save specific onboarding step data.
     */
    public function saveStep(User $user, int $step, array $data): User
    {
        switch ($step) {
            case 1: // Name
                if (!empty($data['name'])) {
                    $user->name = trim($data['name']);
                }
                break;

            case 2: // Birthdate & Age
                if (!empty($data['birth_date'])) {
                    $birthDate = Carbon::parse($data['birth_date']);
                    $user->birth_date = $birthDate->format('Y-m-d');
                    $user->age = $birthDate->age;
                }
                break;

            case 3: // Gender & Looking For
                if (!empty($data['gender'])) {
                    $user->gender = $data['gender'];
                }
                if (!empty($data['looking_for'])) {
                    $user->looking_for = $data['looking_for'];
                }
                break;

            case 4: // City & Geolocation
                if (!empty($data['city'])) {
                    $user->city = trim($data['city']);
                }
                if (isset($data['latitude']) && isset($data['longitude'])) {
                    $user->latitude = $data['latitude'];
                    $user->longitude = $data['longitude'];
                }
                break;

            case 5: // Bio
                if (isset($data['bio'])) {
                    $user->bio = Str::limit(trim($data['bio']), 250, '');
                }
                break;

            case 6: // Finish
                $user->onboarding_completed = true;
                break;
        }

        $user->save();

        return $user->fresh();
    }

    /**
     * Upload user profile photo into model_files table.
     */
    public function uploadPhoto(User $user, UploadedFile $file): string
    {
        $filename = 'photo_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs('photos', $filename, 'public');
        $publicUrl = Storage::disk('public')->url($storedPath);

        $currentPhotosCount = $user->photos()->count();

        if ($currentPhotosCount < 3) {
            ModelFile::create([
                'model_type' => User::class,
                'model_id' => $user->id,
                'file_path' => $publicUrl,
                'file_type' => 'photo',
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'order' => $currentPhotosCount,
                'is_main' => ($currentPhotosCount === 0),
            ]);
        }

        return $publicUrl;
    }

    /**
     * Remove photo from model_files by index.
     */
    public function deletePhoto(User $user, int $photoIndex): User
    {
        $photos = $user->photos()->orderBy('order')->get();

        if (isset($photos[$photoIndex])) {
            $photoToDelete = $photos[$photoIndex];

            // Delete physical file from disk
            $filename = basename($photoToDelete->file_path);
            if (Storage::disk('public')->exists('photos/' . $filename)) {
                Storage::disk('public')->delete('photos/' . $filename);
            }

            $photoToDelete->delete();

            // Re-order remaining photos
            $remaining = $user->photos()->orderBy('id')->get();
            foreach ($remaining as $i => $item) {
                $item->update([
                    'order' => $i,
                    'is_main' => ($i === 0),
                ]);
            }
        }

        return $user->fresh();
    }

    /**
     * Complete onboarding wizard.
     */
    public function completeOnboarding(User $user): User
    {
        $user->update([
            'onboarding_completed' => true,
            'is_terms_accepted' => true,
            'terms_accepted_at' => $user->terms_accepted_at ?? now(),
        ]);

        return $user->fresh();
    }
}
