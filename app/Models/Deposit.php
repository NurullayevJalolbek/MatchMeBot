<?php

namespace App\Models;

use App\Enums\Deposit\DepositStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'receipt_path',
        'status',
        'admin_note',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => DepositStatusEnum::class,
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receiptFiles(): MorphMany
    {
        return $this->morphMany(ModelFile::class, 'model');
    }
}
