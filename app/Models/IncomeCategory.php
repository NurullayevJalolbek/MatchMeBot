<?php

namespace App\Models;

use App\Enums\Finance\FinanceStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $table = 'income_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'description',
        'status',
        'order',
        'is_active',
    ];

    protected $casts = [
        'status' => FinanceStatusEnum::class,
        'is_active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer',
    ];

    /**
     * Parent category relationship.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Subcategories (children) relationship.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order', 'asc');
    }

    /**
     * Scope for main parent categories (parent_id is NULL).
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->orderBy('order', 'asc');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', FinanceStatusEnum::ACTIVE->value)
            ->where('is_active', true)
            ->orderBy('order', 'asc');
    }

    /**
     * Check if category is a parent category.
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get all ancestor categories for recursive breadcrumb path.
     *
     * @return array<IncomeCategory>
     */
    public function getBreadcrumbs(): array
    {
        $crumbs = [];
        $current = $this;

        while ($current) {
            array_unshift($crumbs, $current);
            $current = $current->parent;
        }

        return $crumbs;
    }
}
