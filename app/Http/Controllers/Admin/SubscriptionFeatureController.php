<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iSubscriptionFeatureService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionFeatureStoreRequest;
use App\Http\Requests\Admin\SubscriptionFeatureUpdateRequest;
use App\Models\SubscriptionFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionFeatureController extends Controller
{
    public function __construct(
        protected iSubscriptionFeatureService $featureService
    ) {}

    /**
     * Display a listing of the subscription features.
     */
    public function index(Request $request): View
    {
        $features = $this->featureService->paginateSubscriptionFeatures($request->only(['search', 'status']), 10);

        return view('admin.pages.subscription_features.index', [
            'datas' => $features,
            'features' => $features,
        ]);
    }

    /**
     * Show the form for creating a new feature.
     */
    public function create(): View
    {
        return view('admin.pages.subscription_features.create');
    }

    /**
     * Store a newly created feature in storage.
     */
    public function store(SubscriptionFeatureStoreRequest $request): RedirectResponse
    {
        $this->featureService->createSubscriptionFeature(
            $request->validated(),
            $request->file('icon_file')
        );

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Yangi Obuna afzalligi muvaffaqiyatli yaratildi!');
    }

    /**
     * Show the form for editing the specified feature.
     */
    public function edit(SubscriptionFeature $subscription_feature): View
    {
        return view('admin.pages.subscription_features.edit', [
            'model' => $subscription_feature,
            'feature' => $subscription_feature,
        ]);
    }

    /**
     * Update the specified feature in storage.
     */
    public function update(SubscriptionFeatureUpdateRequest $request, SubscriptionFeature $subscription_feature): RedirectResponse
    {
        $this->featureService->updateSubscriptionFeature(
            $subscription_feature,
            $request->validated(),
            $request->file('icon_file')
        );

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Obuna afzalligi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified feature from storage.
     */
    public function destroy(Request $request, SubscriptionFeature $subscription_feature)
    {
        $this->featureService->deleteSubscriptionFeature($subscription_feature);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Obuna afzalligi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Obuna afzalligi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(SubscriptionFeature $subscription_feature): RedirectResponse
    {
        $this->featureService->toggleSubscriptionFeatureStatus($subscription_feature);

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Obuna afzalligi statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
