<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iBoostService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoostController extends Controller
{
    public function __construct(
        protected iBoostService $boostService
    ) {}

    /**
     * Get user boost plans and current boost status.
     */
    public function getStatus(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $data = $this->boostService->getBoostStatus($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => $data,
        ]);
    }

    /**
     * Activate a boost plan from balance.
     */
    public function activate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'plan_id' => 'required|integer|exists:boost_plans,id',
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

        $planId = (int) $request->input('plan_id');
        $result = $this->boostService->activateBoost($user, $planId);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => $result,
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => $result,
        ]);
    }
}
