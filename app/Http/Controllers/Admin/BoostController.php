<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iBoostService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BoostStoreRequest;
use App\Http\Requests\Admin\BoostUpdateRequest;
use App\Models\BoostPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoostController extends Controller
{
    protected iBoostService $boostService;

    public function __construct(iBoostService $boostService)
    {
        $this->boostService = $boostService;
    }

    /**
     * Display a listing of the boost plans.
     */
    public function index(Request $request): View
    {
        $boosts = $this->boostService->paginateBoostPlans($request->only(['search', 'status']), 10);

        return view('admin.pages.boosts.index', [
            'datas' => $boosts,
            'boosts' => $boosts,
        ]);
    }

    /**
     * Show the form for creating a new boost plan.
     */
    public function create(): View
    {
        $incomeCategories = \App\Models\IncomeCategory::with('children')->parents()->active()->get();

        return view('admin.pages.boosts.create', [
            'incomeCategories' => $incomeCategories,
        ]);
    }

    /**
     * Store a newly created boost plan in storage.
     */
    public function store(BoostStoreRequest $request): RedirectResponse
    {
        $this->boostService->createBoostPlan($request->validated());

        return redirect()->route('admin.boosts.index')
            ->with('success', 'Yangi Boost rejasi muvaffaqiyatli yaratildi!');
    }

    /**
     * Show the form for editing the specified boost plan.
     */
    public function edit(BoostPlan $boost): View
    {
        $incomeCategories = \App\Models\IncomeCategory::with('children')->parents()->active()->get();

        return view('admin.pages.boosts.edit', [
            'model' => $boost,
            'boost' => $boost,
            'incomeCategories' => $incomeCategories,
        ]);
    }

    /**
     * Update the specified boost plan in storage.
     */
    public function update(BoostUpdateRequest $request, BoostPlan $boost): RedirectResponse
    {
        $this->boostService->updateBoostPlan($boost, $request->validated());

        return redirect()->route('admin.boosts.index')
            ->with('success', 'Boost rejasi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified boost plan from storage.
     */
    public function destroy(Request $request, BoostPlan $boost)
    {
        $this->boostService->deleteBoostPlan($boost);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost rejasi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.boosts.index')
            ->with('success', 'Boost rejasi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(BoostPlan $boost): RedirectResponse
    {
        $this->boostService->toggleBoostPlanStatus($boost);

        return redirect()->route('admin.boosts.index')
            ->with('success', 'Boost rejasi statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
