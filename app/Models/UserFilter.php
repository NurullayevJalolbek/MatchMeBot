<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFilter extends Model
{
    use HasFactory;

    protected $table = 'user_filters';

    protected $fillable = [
        'user_id',
        'looking_for',
        'min_age',
        'max_age',
        'max_distance_km',
        'city',
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'max_distance_km' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
