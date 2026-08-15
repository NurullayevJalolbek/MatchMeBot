<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iOnboardingService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    public function __construct(
        protected iOnboardingService $onboardingService
    ) {}

    /**
     * Authenticate or get user from Telegram initData.
     */
    public function init(Request $request): JsonResponse
    {
        $telegramData = $request->input('telegram_user', []);
        $user = $this->onboardingService->getOrCreateTelegramUser($telegramData);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => [
                'user' => $user,
                'is_completed' => (bool) $user->onboarding_completed,
                'is_terms_accepted' => (bool) $user->is_terms_accepted,
            ],
        ]);
    }

    /**
     * Accept Terms and Privacy Policy.
     */
    public function acceptTerms(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $user = $this->onboardingService->acceptTerms($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully updated'),
            'data' => $user,
        ]);
    }

    /**
     * Save onboarding step.
     */
    public function saveStep(Request $request): JsonResponse
    {
        $step = (int) $request->input('step', 1);
        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        // Validation based on step
        $rules = match ($step) {
            1 => ['name' => 'required|string|min:2|max:50'],
            2 => ['birth_date' => 'required|date|before:-18 years'],
            3 => ['gender' => 'required|in:male,female', 'looking_for' => 'required|in:female,male,all'],
            4 => ['city' => 'required|string|max:100'],
            5 => ['bio' => 'nullable|string|max:250'],
            6 => [],
            default => [],
        };

        $messages = [
            'birth_date.before' => "Xizmatdan faqat 18 yoshdan kattalar foydalanishi mumkin.",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->onboardingService->saveStep($user, $step, $request->all());

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully updated'),
            'data' => [
                'user' => $user,
                'photos' => $user->photo_urls,
                'current_step' => $step,
                'next_step' => $step < 6 ? $step + 1 : 'completed',
                'is_completed' => (bool) $user->onboarding_completed,
            ],
        ]);
    }

    /**
     * Upload photo for user profile.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // up to 10MB
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $photoUrl = $this->onboardingService->uploadPhoto($user, $request->file('photo'));

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully stored'),
            'data' => [
                'url' => $photoUrl,
                'photos' => $user->fresh()->photo_urls,
            ],
        ]);
    }

    /**
     * Delete photo by index.
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo_index' => 'required|integer|min:0|max:2',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $user = $this->onboardingService->deletePhoto($user, (int) $request->input('photo_index'));

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully deleted'),
            'data' => [
                'photos' => $user->photo_urls,
            ],
        ]);
    }
}
