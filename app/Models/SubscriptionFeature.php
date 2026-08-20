<?php

namespace App\Models;

use App\Enums\Subscription\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    use HasFactory;

    protected $table = 'subscription_features';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'status',
        'order',
        'is_active',
    ];

    protected $casts = [
        'status' => SubscriptionStatusEnum::class,
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope for active features.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->where('is_active', true)
            ->orderBy('order', 'asc');
    }

    /**
     * Get image/icon URL attribute.
     */
    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
            return $this->icon;
        }

        return asset($this->icon);
    }
}
