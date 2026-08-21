<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Subscription\UserServiceStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSubscriptionController extends Controller
{
    /**
     * Display a listing of user subscriptions.
     */
    public function index(Request $request): View
    {
        $query = UserSubscription::query()->with(['user', 'plan', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%")
                  ->orWhere('telegram_id', 'ilike', "%{$search}%");
            });
        }

        $subscriptions = $query->orderBy('status', 'ASC')
            ->orderBy('ends_at', 'DESC')
            ->paginate(15);

        return view('admin.pages.user_subscriptions.index', [
            'datas' => $subscriptions,
            'subscriptions' => $subscriptions,
            'currentStatus' => $request->get('status'),
        ]);
    }

    /**
     * Cancel active subscription early.
     */
    public function cancel(UserSubscription $userSubscription)
    {
        $userSubscription->update([
            'status' => UserServiceStatusEnum::CANCELLED,
            'is_active' => false,
        ]);

        // Check if user has other active subscriptions
        $hasOther = UserSubscription::where('user_id', $userSubscription->user_id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        if (!$hasOther) {
            User::where('id', $userSubscription->user_id)->update(['is_premium' => false]);
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Obuna muvaffaqiyatli bekor qilindi!',
            ]);
        }

        return redirect()->route('admin.user-subscriptions.index')
            ->with('success', 'Obuna muvaffaqiyatli bekor qilindi!');
    }

    /**
     * Delete user subscription record.
     */
    public function destroy(Request $request, UserSubscription $userSubscription)
    {
        $userSubscription->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Obuna yozuvi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.user-subscriptions.index')
            ->with('success', 'Obuna yozuvi muvaffaqiyatli o\'chirildi!');
    }
}
