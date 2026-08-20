<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iSubscriptionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionStoreRequest;
use App\Http\Requests\Admin\SubscriptionUpdateRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected iSubscriptionService $subscriptionService
    ) {}

    /**
     * Display a listing of the subscription plans.
     */
    public function index(Request $request): View
    {
        $plans = $this->subscriptionService->paginateSubscriptionPlans($request->only(['search', 'status']), 10);

        return view('admin.pages.subscriptions.index', [
            'datas' => $plans,
            'subscriptions' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create(): View
    {
        $incomeCategories = \App\Models\IncomeCategory::with('children')->parents()->active()->get();

        return view('admin.pages.subscriptions.create', [
            'incomeCategories' => $incomeCategories,
        ]);
    }

    /**
     * Store a newly created subscription plan in storage.
     */
    public function store(SubscriptionStoreRequest $request): RedirectResponse
    {
        $this->subscriptionService->createSubscriptionPlan($request->validated());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Yangi Obuna tarifi muvaffaqiyatli yaratildi!');
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $subscription): View
    {
        $incomeCategories = \App\Models\IncomeCategory::with('children')->parents()->active()->get();

        return view('admin.pages.subscriptions.edit', [
            'model' => $subscription,
            'subscription' => $subscription,
            'incomeCategories' => $incomeCategories,
        ]);
    }

    /**
     * Update the specified subscription plan in storage.
     */
    public function update(SubscriptionUpdateRequest $request, SubscriptionPlan $subscription): RedirectResponse
    {
        $this->subscriptionService->updateSubscriptionPlan($subscription, $request->validated());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Obuna tarifi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified subscription plan from storage.
     */
    public function destroy(Request $request, SubscriptionPlan $subscription)
    {
        $this->subscriptionService->deleteSubscriptionPlan($subscription);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Obuna tarifi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Obuna tarifi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(SubscriptionPlan $subscription): RedirectResponse
    {
        $this->subscriptionService->toggleSubscriptionPlanStatus($subscription);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Obuna tarifi statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
