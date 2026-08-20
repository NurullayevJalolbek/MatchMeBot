<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iExpenseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseStoreRequest;
use App\Http\Requests\Admin\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        protected iExpenseService $expenseService
    ) {}

    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request): View
    {
        $expenses = $this->expenseService->paginateExpenses($request->only(['search', 'status', 'category_id']), 15);
        $categories = ExpenseCategory::with('children')->parents()->active()->get();

        return view('admin.pages.expenses.index', [
            'datas' => $expenses,
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(): View
    {
        $categories = ExpenseCategory::with('children')->parents()->active()->get();

        return view('admin.pages.expenses.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(ExpenseStoreRequest $request): RedirectResponse
    {
        $this->expenseService->createExpense($request->validated(), auth()->id());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Yangi Xarajat muvaffaqiyatli saqlandi!');
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense): View
    {
        $categories = ExpenseCategory::with('children')->parents()->active()->get();

        return view('admin.pages.expenses.edit', [
            'model' => $expense,
            'expense' => $expense,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense): RedirectResponse
    {
        $this->expenseService->updateExpense($expense, $request->validated());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Xarajat ma\'lumotlari muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Request $request, Expense $expense)
    {
        $this->expenseService->deleteExpense($expense);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xarajat muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Xarajat muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Toggle status between approved and pending.
     */
    public function toggleStatus(Expense $expense): RedirectResponse
    {
        $this->expenseService->toggleExpenseStatus($expense);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Xarajat statusi muvaffaqiyatli o\'zgartirildi!');
    }
}
