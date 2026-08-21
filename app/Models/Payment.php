<?php

namespace App\Models;

use App\Enums\Finance\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'income_category_id',
        'payable_type',
        'payable_id',
        'amount',
        'receipt_image',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'income_category_id' => 'integer',
        'payable_id' => 'integer',
        'amount' => 'decimal:2',
        'status' => PaymentStatusEnum::class,
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Foydalanuvchi (Telegram user) munosabati.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tushum kategoriyasi munosabati.
     */
    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    /**
     * Sotib olinayotgan xizmat (SubscriptionPlan yoki BoostPlan).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Tasdiqlagan yoki rad etgan admin.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Foydalanuvchi obuna yozuvi.
     */
    public function userSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class, 'payment_id');
    }

    /**
     * Foydalanuvchi boost yozuvi.
     */
    public function userBoost(): HasOne
    {
        return $this->hasOne(UserBoost::class, 'payment_id');
    }

    /**
     * Kutilayotgan (Pending) to'lovlar scope.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PaymentStatusEnum::PENDING->value);
    }

    /**
     * Tasdiqlangan (Approved) to'lovlar scope.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PaymentStatusEnum::APPROVED->value);
    }

    /**
     * Formatlangan summa.
     */
    public function getFormattedAmountAttribute(): string
    {
        return format_price($this->amount);
    }

    /**
     * Formatlangan yaratilgan sana.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return format_datetime($this->created_at);
    }

    /**
     * Chek rasm URL manzili.
     */
    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_image) {
            return null;
        }

        if (str_starts_with($this->receipt_image, 'http')) {
            return $this->receipt_image;
        }

        return asset('storage/' . $this->receipt_image);
    }
}
