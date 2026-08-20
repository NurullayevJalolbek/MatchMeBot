<?php

namespace App\Contracts;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface iExpenseService
{
    /**
     * Paginate expenses list with filtering.
     */
    public function paginateExpenses(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a new expense.
     */
    public function createExpense(array $data, ?int $authorId = null): Expense;

    /**
     * Update an existing expense.
     */
    public function updateExpense(Expense $expense, array $data): Expense;

    /**
     * Delete an expense.
     */
    public function deleteExpense(Expense $expense): bool;

    /**
     * Toggle expense status between approved and pending.
     */
    public function toggleExpenseStatus(Expense $expense): Expense;

    /**
     * Approve a pending expense.
     */
    public function approveExpense(Expense $expense): Expense;
}
