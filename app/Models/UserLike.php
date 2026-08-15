<?php

namespace App\Models;

use App\Enums\Like\LikeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLike extends Model
{
    use HasFactory;

    protected $table = 'user_likes';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'is_gift',
        'gift_name',
        'gift_icon',
        'status',
    ];

    protected $casts = [
        'is_gift' => 'boolean',
        'status' => LikeStatusEnum::class,
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
