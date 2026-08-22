<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iLikesService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikesController extends Controller
{
    public function __construct(
        protected iLikesService $likesService
    ) {}

    /**
     * Get likes & VIP gifts list for user.
     */
    public function getLikes(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $data = $this->likesService->getLikesData($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => $data,
        ]);
    }

    /**
     * Accept a like or gift.
     */
    public function accept(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'like_id' => 'required|integer|exists:user_likes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $result = $this->likesService->acceptLike($user, (int) $request->input('like_id'));

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result,
        ]);
    }

    /**
     * Reject a like or gift.
     */
    public function reject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'like_id' => 'required|integer|exists:user_likes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $result = $this->likesService->rejectLike($user, (int) $request->input('like_id'));

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result,
        ]);
    }
}
