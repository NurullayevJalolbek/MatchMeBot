<?php

namespace App\Services;

use App\Contracts\iIncomeCategoryService;
use App\Enums\Finance\FinanceStatusEnum;
use App\Models\IncomeCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class IncomeCategoryService implements iIncomeCategoryService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = IncomeCategory::class;

    /**
     * Model uchun query builder.
     */
    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Paginate income categories (hierarchical list).
     */
    public function paginateCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        } elseif (empty($filters['search'])) {
            $query->whereNull('parent_id');
        }

        return $query->with(['parent', 'children'])
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get only parent categories for dropdown selector.
     */
    public function getParentCategories(?int $excludeId = null): Collection
    {
        $query = $this->query()->parents()->active();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    /**
     * Create a new income category.
     */
    public function createCategory(array $data): IncomeCategory
    {
        $data = $this->prepareData($data);

        return $this->modelClass::create($data);
    }

    /**
     * Update an existing income category.
     */
    public function updateCategory(IncomeCategory $category, array $data): IncomeCategory
    {
        $data = $this->prepareData($data);

        $category->update($data);

        return $category;
    }

    /**
     * Delete an income category.
     */
    public function deleteCategory(IncomeCategory $category): bool
    {
        return (bool) $category->delete();
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleCategoryStatus(IncomeCategory $category): IncomeCategory
    {
        $currentStatus = $category->status instanceof FinanceStatusEnum ? $category->status->value : ($category->status ?: 'active');
        $newStatus = ($currentStatus === FinanceStatusEnum::ACTIVE->value) ? FinanceStatusEnum::INACTIVE : FinanceStatusEnum::ACTIVE;

        $category->update([
            'status' => $newStatus,
            'is_active' => $newStatus === FinanceStatusEnum::ACTIVE,
        ]);

        return $category;
    }

    /**
     * Clean and prepare data attributes before saving.
     */
    protected function prepareData(array $data): array
    {
        if (empty($data['parent_id'])) {
            $data['parent_id'] = null;
        }

        $statusValue = $data['status'] instanceof FinanceStatusEnum ? $data['status']->value : ($data['status'] ?? 'active');
        $data['status'] = $statusValue;
        $data['is_active'] = $statusValue === FinanceStatusEnum::ACTIVE->value;
        $data['order'] = $data['order'] ?? 0;
        $data['icon'] = $data['icon'] ?: '📁';

        return $data;
    }
}
