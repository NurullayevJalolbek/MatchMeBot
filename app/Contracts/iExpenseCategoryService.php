<?php

namespace App\Contracts;

use App\Models\ExpenseCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface iExpenseCategoryService
{
    /**
     * Paginate expense categories (hierarchical list).
     */
    public function paginateCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get only parent categories for dropdown selector.
     */
    public function getParentCategories(?int $excludeId = null): Collection;

    /**
     * Create a new expense category.
     */
    public function createCategory(array $data): ExpenseCategory;

    /**
     * Update an existing expense category.
     */
    public function updateCategory(ExpenseCategory $category, array $data): ExpenseCategory;

    /**
     * Delete an expense category.
     */
    public function deleteCategory(ExpenseCategory $category): bool;

    /**
     * Toggle status between active and inactive.
     */
    public function toggleCategoryStatus(ExpenseCategory $category): ExpenseCategory;
}
