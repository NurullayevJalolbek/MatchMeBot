<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iExpenseCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseCategoryStoreRequest;
use App\Http\Requests\Admin\ExpenseCategoryUpdateRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        protected iExpenseCategoryService $categoryService
    ) {}

    /**
     * Display a listing of the expense categories (Root or inside a specific parent category).
     */
    public function index(Request $request): View
    {
        $parentId = $request->get('parent_id');
        $parentCategory = $parentId ? ExpenseCategory::with('parent')->find($parentId) : null;

        $categories = $this->categoryService->paginateCategories($request->only(['search', 'status', 'parent_id']), 15);

        return view('admin.pages.expense_categories.index', [
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
        $parentCategory = $parentId ? ExpenseCategory::find($parentId) : null;

        return view('admin.pages.expense_categories.create', [
            'parentCategory' => $parentCategory,
            'parentId' => $parentId,
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(ExpenseCategoryStoreRequest $request): RedirectResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.expense-categories.index', [
            'parent_id' => $category->parent_id,
        ])->with('success', 'Yangi Xarajat kategoriyasi muvaffaqiyatli yaratildi!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ExpenseCategory $expense_category): View
    {
        $parentCategory = $expense_category->parent;

        return view('admin.pages.expense_categories.edit', [
            'model' => $expense_category,
            'category' => $expense_category,
            'parentCategory' => $parentCategory,
            'parentId' => $expense_category->parent_id,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(ExpenseCategoryUpdateRequest $request, ExpenseCategory $expense_category): RedirectResponse
    {
        $this->categoryService->updateCategory($expense_category, $request->validated());

        return redirect()->route('admin.expense-categories.index', [
            'parent_id' => $expense_category->parent_id,
        ])->with('success', 'Xarajat kategoriyasi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, ExpenseCategory $expense_category)
    {
        $parentId = $expense_category->parent_id;
        $this->categoryService->deleteCategory($expense_category);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xarajat kategoriyasi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.expense-categories.index', ['parent_id' => $parentId])
            ->with('success', 'Xarajat kategoriyasi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle category status between active and inactive.
     */
    public function toggleStatus(ExpenseCategory $expense_category): RedirectResponse
    {
        $this->categoryService->toggleCategoryStatus($expense_category);

        return redirect()->route('admin.expense-categories.index', [
            'parent_id' => $expense_category->parent_id,
        ])->with('success', 'Kategoriya statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
