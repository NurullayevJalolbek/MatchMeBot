<?php

namespace App\Contracts;

use App\Models\IncomeCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface iIncomeCategoryService
{
    /**
     * Paginate income categories (hierarchical list).
     */
    public function paginateCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get only parent categories for dropdown selector.
     */
    public function getParentCategories(?int $excludeId = null): Collection;

    /**
     * Create a new income category.
     */
    public function createCategory(array $data): IncomeCategory;

    /**
     * Update an existing income category.
     */
    public function updateCategory(IncomeCategory $category, array $data): IncomeCategory;

    /**
     * Delete an income category.
     */
    public function deleteCategory(IncomeCategory $category): bool;

    /**
     * Toggle status between active and inactive.
     */
    public function toggleCategoryStatus(IncomeCategory $category): IncomeCategory;
}
