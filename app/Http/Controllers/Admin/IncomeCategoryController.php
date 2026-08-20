<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iIncomeCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IncomeCategoryStoreRequest;
use App\Http\Requests\Admin\IncomeCategoryUpdateRequest;
use App\Models\IncomeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeCategoryController extends Controller
{
    public function __construct(
        protected iIncomeCategoryService $categoryService
    ) {}

    /**
     * Display a listing of the income categories (Root or inside a specific parent category).
     */
    public function index(Request $request): View
    {
        $parentId = $request->get('parent_id');
        $parentCategory = $parentId ? IncomeCategory::with('parent')->find($parentId) : null;

        $categories = $this->categoryService->paginateCategories($request->only(['search', 'status', 'parent_id']), 15);

        return view('admin.pages.income_categories.index', [
            'datas' => $categories,
            'categories' => $categories,
            'parentCategory' => $parentCategory,
            'parentId' => $parentId,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(Request $request): View
    {
        $parentId = $request->get('parent_id');
        $parentCategory = $parentId ? IncomeCategory::find($parentId) : null;

        return view('admin.pages.income_categories.create', [
            'parentCategory' => $parentCategory,
            'parentId' => $parentId,
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(IncomeCategoryStoreRequest $request): RedirectResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.income-categories.index', [
            'parent_id' => $category->parent_id,
        ])->with('success', 'Yangi Tushum kategoriyasi muvaffaqiyatli yaratildi!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(IncomeCategory $income_category): View
    {
        $parentCategory = $income_category->parent;

        return view('admin.pages.income_categories.edit', [
            'model' => $income_category,
            'category' => $income_category,
            'parentCategory' => $parentCategory,
            'parentId' => $income_category->parent_id,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(IncomeCategoryUpdateRequest $request, IncomeCategory $income_category): RedirectResponse
    {
        $this->categoryService->updateCategory($income_category, $request->validated());

        return redirect()->route('admin.income-categories.index', [
            'parent_id' => $income_category->parent_id,
        ])->with('success', 'Tushum kategoriyasi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, IncomeCategory $income_category)
    {
        $parentId = $income_category->parent_id;
        $this->categoryService->deleteCategory($income_category);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tushum kategoriyasi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.income-categories.index', ['parent_id' => $parentId])
            ->with('success', 'Tushum kategoriyasi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle category status between active and inactive.
     */
    public function toggleStatus(IncomeCategory $income_category): RedirectResponse
    {
        $this->categoryService->toggleCategoryStatus($income_category);

        return redirect()->route('admin.income-categories.index', [
            'parent_id' => $income_category->parent_id,
        ])->with('success', 'Kategoriya statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
