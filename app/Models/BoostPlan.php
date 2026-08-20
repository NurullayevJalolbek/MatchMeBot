<?php

namespace App\Models;

use App\Enums\Boost\BoostStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostPlan extends Model
{
    use HasFactory;

    protected $table = 'boost_plans';

    protected $fillable = [
        'income_category_id',
        'title',
        'description',
        'name',
        'subtitle',
        'icon',
        'hours',
        'price',
        'original_price',
        'badge',
        'badge_type',
        'status',
        'is_active',
        'order',
    ];

    protected $casts = [
        'income_category_id' => 'integer',
        'hours' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'status' => BoostStatusEnum::class,
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Income category relationship.
     */
    public function incomeCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    /**
     * Get display title.
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: ($this->name ?: ($this->hours . ' soatlik boost'));
    }

    /**
     * Get formatted price string.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, '.', ' ') . ' UZS';
    }

    /**
     * Get formatted original price string.
     */
    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price ? number_format((float) $this->original_price, 0, '.', ' ') . ' UZS' : null;
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BoostStatusEnum::ACTIVE->value)
                     ->orWhere(function ($q) {
                         $q->whereNull('status')->where('is_active', true);
                     })
                     ->orderBy('order');
    }
}
