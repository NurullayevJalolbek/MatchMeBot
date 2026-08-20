<?php

namespace App\Services;

use App\Contracts\iExpenseService;
use App\Enums\Finance\ExpenseStatusEnum;
use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ExpenseService implements iExpenseService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = Expense::class;

    /**
     * Model uchun query builder.
     */
    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Paginate expenses list with filtering.
     */
    public function paginateExpenses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()->with(['category.parent', 'author']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('payment_method', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('expense_category_id', $filters['category_id']);
        }

        return $query->orderBy('spent_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Create a new expense.
     */
    public function createExpense(array $data, ?int $authorId = null): Expense
    {
        if ($authorId) {
            $data['user_id'] = $authorId;
        }

        return $this->modelClass::create($data);
    }

    /**
     * Update an existing expense.
     */
    public function updateExpense(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense;
    }

    /**
     * Delete an expense.
     */
    public function deleteExpense(Expense $expense): bool
    {
        return (bool) $expense->delete();
    }

    /**
     * Toggle expense status between approved and pending.
     */
    public function toggleExpenseStatus(Expense $expense): Expense
    {
        $currentStatus = $expense->status instanceof ExpenseStatusEnum 
            ? $expense->status->value 
            : ($expense->status ?: 'approved');

        $newStatus = ($currentStatus === ExpenseStatusEnum::APPROVED->value) 
            ? ExpenseStatusEnum::PENDING 
            : ExpenseStatusEnum::APPROVED;

        $expense->update([
            'status' => $newStatus,
        ]);

        return $expense;
    }

    /**
     * Approve a pending expense.
     */
    public function approveExpense(Expense $expense): Expense
    {
        $expense->update([
            'status' => ExpenseStatusEnum::APPROVED,
        ]);

        return $expense;
    }
}
