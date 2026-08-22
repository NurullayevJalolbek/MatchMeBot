<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iDiscoveryService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscoveryController extends Controller
{
    public function __construct(
        protected iDiscoveryService $discoveryService
    ) {}

    /**
     * Get user filter preferences.
     */
    public function getFilter(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $filter = $this->discoveryService->getFilter($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => $filter,
        ]);
    }

    /**
     * Save user filter preferences.
     */
    public function saveFilter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'looking_for' => 'required|in:female,male,all',
            'min_age' => 'required|integer|min:18|max:99',
            'max_age' => 'required|integer|gte:min_age|max:99',
            'max_distance_km' => 'required|integer|min:1|max:500',
            'city' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $filter = $this->discoveryService->saveFilter($user, $request->all());

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully updated'),
            'data' => $filter,
        ]);
    }

    /**
     * Get candidate profiles for discovery swiping.
     */
    public function getCandidates(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $candidates = $this->discoveryService->getCandidates($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => $candidates,
        ]);
    }
}
