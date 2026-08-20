<?php

namespace App\Models;

use App\Enums\Finance\ExpenseStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'expense_category_id',
        'user_id',
        'title',
        'amount',
        'payment_method',
        'spent_at',
        'status',
        'receipt_file',
        'description',
    ];

    protected $casts = [
        'expense_category_id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'decimal:2',
        'status' => ExpenseStatusEnum::class,
        'payment_method' => \App\Enums\Finance\PaymentMethodEnum::class,
        'spent_at' => 'datetime',
    ];

    /**
     * Expense category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    /**
     * Author (admin user) relationship.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for approved expenses.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatusEnum::APPROVED->value);
    }

    /**
     * Scope for pending expenses.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatusEnum::PENDING->value);
    }

    /**
     * Get formatted amount attribute.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 0, '.', ' ') . ' UZS';
    }

    /**
     * Get formatted spent at date attribute.
     */
    public function getFormattedSpentAtAttribute(): string
    {
        return $this->spent_at ? $this->spent_at->format('d.m.Y H:i') : '—';
    }
}
