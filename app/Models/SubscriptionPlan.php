<?php

namespace App\Models;

use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'period_count',
        'period_type',
        'days',
        'price',
        'original_price',
        'badge',
        'status',
        'order',
        'is_active',
    ];

    protected $casts = [
        'status' => SubscriptionStatusEnum::class,
        'period_type' => SubscriptionPeriodTypeEnum::class,
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'period_count' => 'integer',
        'days' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Scope for active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->where('is_active', true)
            ->orderBy('order', 'asc');
    }

    /**
     * Formatted price attribute (e.g. 20 000 UZS).
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, '.', ' ') . ' UZS';
    }

    /**
     * Formatted original price attribute.
     */
    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (!$this->original_price) {
            return null;
        }

        return number_format((float) $this->original_price, 0, '.', ' ') . ' UZS';
    }

    /**
     * Formatted human-readable period (e.g. "1 oylik", "7 kunlik", "3 oylik").
     */
    public function getFormattedPeriodAttribute(): string
    {
        $count = $this->period_count ?: 1;
        $type = $this->period_type instanceof SubscriptionPeriodTypeEnum 
            ? $this->period_type->label() 
            : ($this->period_type ?: 'Oy');

        return "{$count} " . mb_strtolower($type) . "lik";
    }

    /**
     * Approximate daily price string (e.g. "kuniga ~1 000 UZS").
     */
    public function getDailyPriceAttribute(): string
    {
        $days = max(1, $this->days ?: 30);
        $daily = round((float) $this->price / $days);

        return "kuniga ~" . number_format($daily, 0, '.', ' ') . " UZS";
    }
}
