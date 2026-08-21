<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iProfileOptionService;
use App\Enums\Profile\ProfileOptionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileOptionStoreRequest;
use App\Http\Requests\Admin\ProfileOptionUpdateRequest;
use App\Models\ProfileOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileOptionController extends Controller
{
    public function __construct(
        protected iProfileOptionService $optionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $currentType = $request->get('type');
        $currentCategory = $request->get('category');
        $search = $request->get('search');

        $options = $this->optionService->getPaginated([
            'type' => $currentType,
            'category' => $currentCategory,
            'search' => $search,
        ], 20);

        // Get distinct categories for filtering
        $categories = ProfileOption::when($currentType, function ($q, $t) {
                return $q->where('type', $t);
            })
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $typeEnum = $currentType ? ProfileOptionTypeEnum::tryFrom($currentType) : null;
        $title = $typeEnum ? $typeEnum->label() : "Ma'lumotnomalar";

        return view('admin.pages.profile_options.index', [
            'datas' => $options,
            'options' => $options,
            'currentType' => $currentType,
            'currentCategory' => $currentCategory,
            'categories' => $categories,
            'types' => ProfileOptionTypeEnum::cases(),
            'title' => $title,
            'typeEnum' => $typeEnum,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $defaultType = $request->get('type');
        $existingCategories = ProfileOption::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.pages.profile_options.create', [
            'types' => ProfileOptionTypeEnum::cases(),
            'defaultType' => $defaultType,
            'existingCategories' => $existingCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfileOptionStoreRequest $request): RedirectResponse
    {
        $option = $this->optionService->create($request->validated());

        return redirect()->route('admin.profile-options.index', ['type' => $option->type->value])
            ->with('success', "Parametr muvaffaqiyatli qo'shildi!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfileOption $profileOption): View
    {
        $existingCategories = ProfileOption::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.pages.profile_options.edit', [
            'option' => $profileOption,
            'data' => $profileOption,
            'types' => ProfileOptionTypeEnum::cases(),
            'existingCategories' => $existingCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileOptionUpdateRequest $request, ProfileOption $profileOption): RedirectResponse
    {
        $this->optionService->update($profileOption, $request->validated());

        return redirect()->route('admin.profile-options.index', ['type' => $profileOption->type->value])
            ->with('success', "Parametr muvaffaqiyatli yangilandi!");
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(ProfileOption $profileOption): JsonResponse|RedirectResponse
    {
        $this->optionService->toggleStatus($profileOption);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Holat muvaffaqiyatli o'zgartirildi!",
                'is_active' => $profileOption->is_active,
            ]);
        }

        return back()->with('success', "Holat muvaffaqiyatli o'zgartirildi!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProfileOption $profileOption): JsonResponse|RedirectResponse
    {
        $this->optionService->delete($profileOption);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Parametr muvaffaqiyatli o'chirildi!",
            ]);
        }

        return redirect()->route('admin.profile-options.index', ['type' => $profileOption->type->value])
            ->with('success', "Parametr muvaffaqiyatli o'chirildi!");
    }
}
