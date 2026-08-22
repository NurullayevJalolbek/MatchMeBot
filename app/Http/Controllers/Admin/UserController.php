<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iUserManagementService;
use App\Enums\Boost\BoostStatusEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\BoostPlan;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected iUserManagementService $userService
    ) {}

    /**
     * Display a listing of bot regular users.
     */
    public function index(Request $request): View
    {
        $users = $this->userService->getPaginatedUsers($request->all(), 15);
        $stats = $this->userService->getUserStatistics();

        return view('admin.pages.users.index', [
            'title' => 'Foydalanuvchilar',
            'users' => $users,
            'stats' => $stats,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Display the specified user profile details.
     */
    public function show(int $id): View
    {
        $user = $this->userService->getUserDetails($id);
        $subscriptionPlans = SubscriptionPlan::where('status', SubscriptionStatusEnum::ACTIVE)
            ->orderBy('order')
            ->get();
        $boostPlans = BoostPlan::where('status', BoostStatusEnum::ACTIVE)
            ->orderBy('order')
            ->get();

        return view('admin.pages.users.show', [
            'title' => 'Foydalanuvchi Profili: ' . $user->full_name,
            'user' => $user,
            'subscriptionPlans' => $subscriptionPlans,
            'boostPlans' => $boostPlans,
        ]);
    }

    /**
     * Toggle user status (Active / Inactive / Blocked).
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $success = $this->userService->toggleStatus($id);

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchi holati muvaffaqiyatli o'zgartirildi!",
        ]);
    }

    /**
     * Grant subscription / VIP to user.
     */
    public function grantSubscription(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:subscription_plans,id',
        ]);

        $success = $this->userService->grantSubscription($id, (int) $request->input('plan_id'));

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchiga obuna (VIP) muvaffaqiyatli berildi!",
        ]);
    }

    /**
     * Revoke subscription / VIP from user.
     */
    public function revokeSubscription(int $id): JsonResponse
    {
        $success = $this->userService->revokeSubscription($id);

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchining obunasi (VIP) muvaffaqiyatli bekor qilindi!",
        ]);
    }

    /**
     * Grant boost to user.
     */
    public function grantBoost(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:boost_plans,id',
        ]);

        $success = $this->userService->grantBoost($id, (int) $request->input('plan_id'));

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchiga Boost xizmati muvaffaqiyatli ulandi!",
        ]);
    }

    /**
     * Revoke boost from user.
     */
    public function revokeBoost(int $id): JsonResponse
    {
        $success = $this->userService->revokeBoost($id);

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchining Boost xizmati muvaffaqiyatli to'xtatildi!",
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $success = $this->userService->deleteUser($id);

        return response()->json([
            'success' => $success,
            'message' => "Foydalanuvchi tizimdan muvaffaqiyatli o'chirildi!",
        ]);
    }
}
