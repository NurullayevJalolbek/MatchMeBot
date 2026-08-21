<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Subscription\UserServiceStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\UserBoost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserBoostController extends Controller
{
    /**
     * Display a listing of user boosts.
     */
    public function index(Request $request): View
    {
        $query = UserBoost::query()->with(['user', 'plan', 'payment']);

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

        $boosts = $query->orderBy('status', 'ASC')
            ->orderBy('ends_at', 'DESC')
            ->paginate(15);

        return view('admin.pages.user_boosts.index', [
            'datas' => $boosts,
            'boosts' => $boosts,
            'currentStatus' => $request->get('status'),
        ]);
    }

    /**
     * Cancel active boost early.
     */
    public function cancel(UserBoost $userBoost)
    {
        $userBoost->update([
            'status' => UserServiceStatusEnum::CANCELLED,
            'is_active' => false,
        ]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost xizmati bekor qilindi!',
            ]);
        }

        return redirect()->route('admin.user-boosts.index')
            ->with('success', 'Boost xizmati bekor qilindi!');
    }

    /**
     * Delete user boost record.
     */
    public function destroy(Request $request, UserBoost $userBoost)
    {
        $userBoost->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost yozuvi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.user-boosts.index')
            ->with('success', 'Boost yozuvi muvaffaqiyatli o\'chirildi!');
    }
}
