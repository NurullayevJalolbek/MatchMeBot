<?php

namespace App\Services;

use App\Contracts\iProfileService;
use App\Enums\Boost\BoostStatusEnum;
use App\Enums\Like\LikeStatusEnum;
use App\Enums\Profile\ProfileOptionTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Enums\User\GenderEnum;
use App\Models\BoostPlan;
use App\Models\District;
use App\Models\ModelFile;
use App\Models\ProfileOption;
use App\Models\Region;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserLike;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileService implements iProfileService
{
    /**
     * Get complete profile data and statistics for Mini-App profile screen.
     */
    public function getProfile(User $user): array
    {
        $user->loadMissing([
            'photos',
            'filter',
            'subscriptions.plan',
            'boosts.plan',
            'livingRegion',
            'livingDistrict',
            'birthRegion',
            'birthDistrict',
            'profileOptions',
        ]);

        // 1. Statistics
        $likesCount = UserLike::where('to_user_id', $user->id)->count();

        $matchesCount = UserLike::where(function ($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->orWhere('from_user_id', $user->id);
        })->where('status', LikeStatusEnum::ACCEPTED)->count();

        $daysCount = max(1, (int) Carbon::parse($user->created_at)->diffInDays(Carbon::now()) + 1);

        // 2. Profile completion percentage
        $completion = $this->calculateCompletion($user);

        // 3. Plans & Features
        $subscriptionPlans = SubscriptionPlan::where('status', SubscriptionStatusEnum::ACTIVE)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $subscriptionFeatures = \App\Models\SubscriptionFeature::where('status', SubscriptionStatusEnum::ACTIVE)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $boostPlans = BoostPlan::where('status', BoostStatusEnum::ACTIVE)
            ->orderBy('order')
            ->get();

        $isVipActive = $user->is_vip && ($user->vip_expires_at === null || $user->vip_expires_at->isFuture());
        $isBoostActive = $user->boost_expires_at && $user->boost_expires_at->isFuture();

        $livingLocationLabel = $user->livingDistrict
            ? "{$user->livingRegion?->name_uz}, {$user->livingDistrict?->name_uz}"
            : ($user->livingRegion?->name_uz ?? ($user->city ?? 'Toshkent shahri'));

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'age' => $user->age,
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'gender' => $user->gender?->value,
                'gender_label' => $user->gender?->label(),
                'height' => $user->height,
                'weight' => $user->weight,
                'city' => $user->city,
                'city_label' => $livingLocationLabel,
                'living_region_id' => $user->living_region_id,
                'living_district_id' => $user->living_district_id,
                'birth_region_id' => $user->birth_region_id,
                'birth_district_id' => $user->birth_district_id,
                'bio' => $user->bio,
                'instagram_username' => $user->instagram_username,
                'primary_photo_url' => $user->primary_photo_url,
                'photos' => $user->photos->map(fn ($p) => [
                    'id' => $p->id,
                    'url' => $p->url,
                    'is_main' => $p->is_main,
                    'order' => $p->order,
                ])->toArray(),
                'is_vip' => $isVipActive,
                'vip_expires_at' => $user->vip_expires_at?->format('d.m.Y H:i'),
                'is_boost' => $isBoostActive,
                'boost_expires_at' => $user->boost_expires_at?->format('d.m.Y H:i'),
                'is_verified' => (bool) $user->is_verified,
            ],
            'stats' => [
                'likes_count' => $likesCount,
                'matches_count' => $matchesCount,
                'days_count' => $daysCount,
            ],
            'completion' => $completion,
            'subscription_plans' => $subscriptionPlans,
            'subscription_features' => $subscriptionFeatures,
            'boost_plans' => $boostPlans,
            'filter' => $user->filter,
        ];
    }

    /**
     * Get all reference options, regions, districts and user's current selections for Edit Profile screen.
     */
    public function getEditProfileData(User $user): array
    {
        $user->loadMissing([
            'photos',
            'livingRegion',
            'livingDistrict',
            'birthRegion',
            'birthDistrict',
            'profileOptions',
        ]);

        $regions = Region::where('is_active', true)->orderBy('order')->get(['id', 'name_uz', 'name_ru', 'order']);
        $districts = District::where('is_active', true)->orderBy('order')->get(['id', 'region_id', 'name_uz', 'name_ru', 'order']);

        // Group options by type & category
        $options = ProfileOption::where('is_active', true)->orderBy('order')->get();
        $groupedOptions = [
            'interests' => $options->where('type', ProfileOptionTypeEnum::INTEREST->value)->groupBy('category')->toArray(),
            'dating_purpose' => $options->where('type', ProfileOptionTypeEnum::DATING_PURPOSE->value)->values()->toArray(),
            'lifestyle' => $options->where('type', ProfileOptionTypeEnum::LIFESTYLE->value)->groupBy('category')->toArray(),
            'about_me' => $options->where('type', ProfileOptionTypeEnum::ABOUT_ME->value)->groupBy('category')->toArray(),
            'marital_status' => $options->where('type', ProfileOptionTypeEnum::MARITAL_STATUS->value)->values()->toArray(),
            'language' => $options->where('type', ProfileOptionTypeEnum::LANGUAGE->value)->values()->toArray(),
        ];

        $selectedOptionIds = $user->profileOptions->pluck('id')->toArray();
        $completion = $this->calculateCompletion($user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name,
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'age' => $user->age,
                'gender' => $user->gender?->value,
                'height' => $user->height ?? 175,
                'weight' => $user->weight ?? 70,
                'bio' => $user->bio,
                'living_region_id' => $user->living_region_id,
                'living_district_id' => $user->living_district_id,
                'birth_region_id' => $user->birth_region_id,
                'birth_district_id' => $user->birth_district_id,
                'is_verified' => (bool) $user->is_verified,
                'photos' => $user->photos->map(fn ($p) => [
                    'id' => $p->id,
                    'url' => $p->url,
                    'is_main' => (bool) $p->is_main,
                    'order' => $p->order,
                ])->toArray(),
                'selected_option_ids' => $selectedOptionIds,
            ],
            'regions' => $regions,
            'districts' => $districts,
            'options' => $groupedOptions,
            'completion' => $completion,
        ];
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [];

            if (isset($data['name'])) {
                $updateData['name'] = trim($data['name']);
            }
            if (isset($data['gender'])) {
                $updateData['gender'] = GenderEnum::tryFrom($data['gender']);
            }
            if (isset($data['birth_date'])) {
                $updateData['birth_date'] = $data['birth_date'];
                try {
                    $birthDate = Carbon::parse($data['birth_date']);
                    $updateData['age'] = (int) $birthDate->diffInYears(Carbon::now());
                } catch (\Exception $e) {}
            }
            if (isset($data['height'])) {
                $updateData['height'] = (int) $data['height'];
            }
            if (isset($data['weight'])) {
                $updateData['weight'] = (int) $data['weight'];
            }
            if (isset($data['bio'])) {
                $updateData['bio'] = mb_substr(trim($data['bio']), 0, 250);
            }
            if (isset($data['living_region_id'])) {
                $updateData['living_region_id'] = $data['living_region_id'] ?: null;
            }
            if (isset($data['living_district_id'])) {
                $updateData['living_district_id'] = $data['living_district_id'] ?: null;
            }
            if (isset($data['birth_region_id'])) {
                $updateData['birth_region_id'] = $data['birth_region_id'] ?: null;
            }
            if (isset($data['birth_district_id'])) {
                $updateData['birth_district_id'] = $data['birth_district_id'] ?: null;
            }
            if (isset($data['city'])) {
                $updateData['city'] = $data['city'];
            }

            $user->update($updateData);

            // Sync profile options (many-to-many)
            if (isset($data['option_ids']) && is_array($data['option_ids'])) {
                $user->profileOptions()->sync($data['option_ids']);
            }

            // Recalculate completion percentage & update verification
            $completion = $this->calculateCompletion($user);
            $user->update([
                'profile_completion_percentage' => $completion['percentage'],
                'is_verified' => $completion['percentage'] >= 100 ? true : $user->is_verified,
            ]);

            return $user->fresh(['photos', 'livingRegion', 'livingDistrict', 'birthRegion', 'birthDistrict', 'profileOptions']);
        });
    }

    /**
     * Link Instagram username to profile.
     */
    public function linkInstagram(User $user, string $instagramUsername): User
    {
        $cleanUsername = ltrim(trim($instagramUsername), '@');
        $user->update(['instagram_username' => $cleanUsername]);

        $this->calculateCompletion($user);

        return $user;
    }

    /**
     * Upload a profile photo (max 3 allowed).
     */
    public function uploadPhoto(User $user, UploadedFile $file, bool $isMain = false, ?int $replacePhotoId = null): array
    {
        if ($replacePhotoId) {
            $oldPhoto = $user->photos()->find($replacePhotoId);
            if ($oldPhoto) {
                if (Storage::disk('public')->exists($oldPhoto->file_path)) {
                    Storage::disk('public')->delete($oldPhoto->file_path);
                }
                $oldPhoto->delete();
            }
        }

        $existingCount = $user->photos()->count();
        if ($existingCount >= 3) {
            $excess = $user->photos()->where('is_main', false)->orderBy('id', 'desc')->first() 
                ?? $user->photos()->orderBy('id', 'desc')->first();
            if ($excess) {
                if (Storage::disk('public')->exists($excess->file_path)) {
                    Storage::disk('public')->delete($excess->file_path);
                }
                $excess->delete();
            }
        }

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'public');

        $isFirst = $user->photos()->count() === 0;
        if ($isFirst || $isMain) {
            $user->photos()->update(['is_main' => false]);
            $isMain = true;
        }

        $modelFile = $user->photos()->create([
            'file_path' => $path,
            'file_type' => 'photo',
            'file_name' => $filename,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'order' => $user->photos()->count() + 1,
            'is_main' => $isMain,
        ]);

        $this->calculateCompletion($user);

        return [
            'id' => $modelFile->id,
            'url' => $modelFile->url,
            'is_main' => (bool) $modelFile->is_main,
            'order' => $modelFile->order,
        ];
    }

    /**
     * Delete a profile photo.
     */
    public function deletePhoto(User $user, int $photoId): bool
    {
        $photo = $user->photos()->find($photoId);
        if (!$photo) {
            return false;
        }

        $wasMain = $photo->is_main;
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
        $photo->delete();

        if ($wasMain) {
            $nextPhoto = $user->photos()->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_main' => true]);
            }
        }

        $this->calculateCompletion($user);

        return true;
    }

    /**
     * Set a photo as primary/main.
     */
    public function setPrimaryPhoto(User $user, int $photoId): bool
    {
        $photo = $user->photos()->find($photoId);
        if (!$photo) {
            return false;
        }

        $user->photos()->update(['is_main' => false]);
        $photo->update(['is_main' => true]);

        return true;
    }

    /**
     * Calculate and sync user's profile completion percentage.
     */
    public function calculateCompletion(User $user): array
    {
        $percentage = 0;
        $missing = [];

        // 1. Rasmlar (Maksimal 25%)
        $photosCount = $user->photos()->count();
        if ($photosCount >= 3) {
            $percentage += 25;
        } elseif ($photosCount == 2) {
            $percentage += 20;
            $missing[] = 'Yana 1 ta rasm';
        } elseif ($photosCount == 1) {
            $percentage += 15;
            $missing[] = 'Yana 2 ta rasm';
        } else {
            $missing[] = 'Kamida 1 ta rasm';
        }

        // 2. Asosiy ma'lumotlar (15%)
        if (!empty($user->name) || !empty($user->first_name)) {
            $percentage += 5;
        } else {
            $missing[] = 'Ism';
        }

        if (!empty($user->birth_date) || !empty($user->age)) {
            $percentage += 5;
        } else {
            $missing[] = 'Tug\'ilgan sana';
        }

        if (!empty($user->gender)) {
            $percentage += 5;
        } else {
            $missing[] = 'Jins';
        }

        // 3. Hududlar (Yashash joyi: 10%, Tug'ilgan joyi: 5% = 15%)
        if (!empty($user->living_region_id)) {
            $percentage += 10;
        } elseif (!empty($user->city)) {
            $percentage += 7;
        } else {
            $missing[] = 'Yashash joyi (Viloyat/Tuman)';
        }

        if (!empty($user->birth_region_id)) {
            $percentage += 5;
        } else {
            $missing[] = 'Tug\'ilgan joyi';
        }

        // 4. Bio (10%)
        if (!empty($user->bio) && mb_strlen($user->bio) >= 10) {
            $percentage += 10;
        } else {
            $missing[] = 'Bio (O\'zingiz haqingizda)';
        }

        // 5. Bo'y va Vazn (5%)
        if (!empty($user->height) && !empty($user->weight)) {
            $percentage += 5;
        } else {
            $missing[] = 'Bo\'y va vazn';
        }

        // 6. Qiziqishlar va Variantlar (Maksimal 30%)
        $selectedOptions = $user->profileOptions()->get();
        $interestsCount = $selectedOptions->where('type', ProfileOptionTypeEnum::INTEREST->value)->count();
        if ($interestsCount >= 3) {
            $percentage += 10;
        } elseif ($interestsCount > 0) {
            $percentage += 5;
            $missing[] = 'Yana ' . (3 - $interestsCount) . ' ta qiziqish';
        } else {
            $missing[] = 'Qiziqishlar (Kamida 3 ta)';
        }

        // Tanishishdan maqsad (5%)
        $hasPurpose = $selectedOptions->where('type', ProfileOptionTypeEnum::DATING_PURPOSE->value)->isNotEmpty();
        if ($hasPurpose) {
            $percentage += 5;
        } else {
            $missing[] = 'Tanishishdan maqsad';
        }

        // Turmush tarzi (10%)
        $hasLifestyle = $selectedOptions->where('type', ProfileOptionTypeEnum::LIFESTYLE->value)->isNotEmpty();
        if ($hasLifestyle) {
            $percentage += 10;
        } else {
            $missing[] = 'Turmush tarzi';
        }

        // Men haqimda ko'proq (5%)
        $hasAboutMe = $selectedOptions->where('type', ProfileOptionTypeEnum::ABOUT_ME->value)->isNotEmpty();
        if ($hasAboutMe) {
            $percentage += 5;
        } else {
            $missing[] = 'Ta\'lim yoki muloqot uslubi';
        }

        $percentage = min(100, max(0, $percentage));

        return [
            'percentage' => $percentage,
            'missing' => $missing,
            'is_complete' => $percentage >= 100,
        ];
    }

    /**
     * Get user expenses & transaction history.
     */
    public function getExpensesHistory(User $user): array
    {
        $user->loadMissing([
            'subscriptions.plan',
            'boosts.plan',
            'payments.incomeCategory',
        ]);

        return [
            'subscriptions' => $user->subscriptions()->with('plan')->latest()->take(20)->get(),
            'boosts' => $user->boosts()->with('plan')->latest()->take(20)->get(),
            'payments' => $user->payments()->with('incomeCategory')->latest()->take(20)->get(),
        ];
    }
}
