<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostPlan extends Model
{
    use HasFactory;

    protected $table = 'boost_plans';

    protected $fillable = [
        'name',
        'subtitle',
        'icon',
        'hours',
        'price',
        'original_price',
        'badge',
        'badge_type',
        'is_active',
        'order',
    ];

    protected $casts = [
        'hours' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' UZS';
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price ? number_format($this->original_price, 0, '.', ' ') . ' UZS' : null;
    }
}
