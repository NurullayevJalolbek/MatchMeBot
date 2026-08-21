<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'payment_id',
        'starts_at',
        'ends_at',
        'status',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'subscription_plan_id' => 'integer',
        'payment_id' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => \App\Enums\Subscription\UserServiceStatusEnum::class,
        'is_active' => 'boolean',
    ];

    /**
     * Foydalanuvchi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obuna tarifi.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * To'lov yozuvi.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Faol obunalar scope.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where('ends_at', '>', now());
    }

    /**
     * Muddati tugaganlik holati.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
