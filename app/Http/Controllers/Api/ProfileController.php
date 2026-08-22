<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iProfileService;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected iProfileService $profileService
    ) {}

    /**
     * Resolve user from request (auth, user_id, telegram_id or fallback).
     */
    protected function resolveUser(Request $request): ?User
    {
        if (auth()->check()) {
            return auth()->user();
        }

        $userId = $request->input('user_id') ?? $request->header('X-User-Id') ?? session('user_id');
        if ($userId && $user = User::find($userId)) {
            return $user;
        }

        $telegramId = $request->input('telegram_id') ?? $request->header('X-Telegram-Id');
        if ($telegramId && $user = User::where('telegram_id', $telegramId)->first()) {
            return $user;
        }

        if ($tgUser = $request->input('telegram_user')) {
            if (isset($tgUser['id']) && $user = User::where('telegram_id', $tgUser['id'])->first()) {
                return $user;
            }
        }

        return User::regularUsers()->latest('id')->first() ?? User::latest('id')->first();
    }

    /**
     * Get user profile details and statistics.
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $data = $this->profileService->getProfile($user);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get full data for the Edit Profile screen (all options, regions, districts and current selections).
     */
    public function getEditData(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $data = $this->profileService->getEditProfileData($user);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        // Convert empty strings to null
        $inputs = $request->all();
        foreach (['living_region_id', 'living_district_id', 'birth_region_id', 'birth_district_id', 'height', 'weight', 'birth_date', 'gender', 'bio', 'name'] as $field) {
            if (array_key_exists($field, $inputs) && ($inputs[$field] === '' || $inputs[$field] === 'null')) {
                $inputs[$field] = null;
            }
        }
        $request->merge($inputs);

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female',
            'birth_date' => 'nullable|date',
            'height' => 'nullable|integer|min:100|max:240',
            'weight' => 'nullable|integer|min:35|max:200',
            'bio' => 'nullable|string|max:250',
            'living_region_id' => 'nullable|integer|exists:regions,id',
            'living_district_id' => 'nullable|integer|exists:districts,id',
            'birth_region_id' => 'nullable|integer|exists:regions,id',
            'birth_district_id' => 'nullable|integer|exists:districts,id',
            'option_ids' => 'nullable|array',
            'option_ids.*' => 'integer|exists:profile_options,id',
        ]);

        $updatedUser = $this->profileService->updateProfile($user, $validated);
        $profileData = $this->profileService->getProfile($updatedUser);

        return response()->json([
            'status' => true,
            'message' => 'Profil muvaffaqiyatli saqlandi!',
            'data' => $profileData,
        ]);
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
            'is_main' => 'nullable|boolean',
            'replace_photo_id' => 'nullable|integer',
        ]);

        try {
            $photo = $this->profileService->uploadPhoto(
                $user,
                $request->file('photo'),
                (bool) $request->input('is_main', false),
                $request->input('replace_photo_id') ? (int) $request->input('replace_photo_id') : null
            );

            $completion = $this->profileService->calculateCompletion($user);

            return response()->json([
                'status' => true,
                'message' => 'Rasm muvaffaqiyatli yuklandi!',
                'data' => [
                    'photo' => $photo,
                    'completion' => $completion,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a profile photo.
     */
    public function deletePhoto(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $deleted = $this->profileService->deletePhoto($user, $id);

        if (!$deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Rasm topilmadi yoki o\'chirib bo\'lmadi',
            ], 404);
        }

        $completion = $this->profileService->calculateCompletion($user);

        return response()->json([
            'status' => true,
            'message' => 'Rasm muvaffaqiyatli o\'chirildi!',
            'data' => [
                'completion' => $completion,
            ],
        ]);
    }

    /**
     * Set a photo as primary.
     */
    public function setPrimaryPhoto(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $success = $this->profileService->setPrimaryPhoto($user, $id);

        return response()->json([
            'status' => $success,
            'message' => $success ? 'Asosiy rasm o\'zgartirildi!' : 'Rasm topilmadi',
        ]);
    }

    /**
     * Link Instagram username to profile.
     */
    public function linkInstagram(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $request->validate([
            'instagram_username' => 'required|string|max:50',
        ]);

        $updatedUser = $this->profileService->linkInstagram(
            $user,
            $request->input('instagram_username')
        );

        return response()->json([
            'status' => true,
            'message' => 'Instagram akkaunti muvaffaqiyatli ulandi!',
            'data' => [
                'user' => [
                    'instagram_username' => $updatedUser->instagram_username,
                ],
            ],
        ]);
    }

    /**
     * Get user expenses history.
     */
    public function getExpenses(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $data = $this->profileService->getExpensesHistory($user);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get districts for a region.
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $regionId = $request->query('region_id');
        $query = District::where('is_active', true)->orderBy('order');

        if ($regionId) {
            $query->where('region_id', $regionId);
        }

        return response()->json([
            'status' => true,
            'data' => $query->get(['id', 'region_id', 'name_uz', 'name_ru']),
        ]);
    }

    /**
     * Submit subscription payment receipt screenshot.
     */
    public function submitReceipt(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ], 404);
        }

        $request->validate([
            'plan_id' => 'required|integer|exists:subscription_plans,id',
            'receipt' => 'required|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
            'notes' => 'nullable|string|max:500',
        ]);

        $plan = \App\Models\SubscriptionPlan::findOrFail($request->input('plan_id'));

        $filename = \Illuminate\Support\Str::random(24) . '.' . $request->file('receipt')->getClientOriginalExtension();
        $path = $request->file('receipt')->storeAs('receipts', $filename, 'public');

        $payment = \App\Models\Payment::create([
            'user_id' => $user->id,
            'income_category_id' => $plan->income_category_id,
            'payable_type' => \App\Models\SubscriptionPlan::class,
            'payable_id' => $plan->id,
            'amount' => $plan->price,
            'receipt_image' => $path,
            'status' => \App\Enums\Finance\PaymentStatusEnum::PENDING,
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'To\'lov cheki muvaffaqiyatli qabul qilindi! Admin tekshiruvidan so\'ng obunangiz faollashtiriladi ✨',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status->value,
            ]
        ]);
    }
}
