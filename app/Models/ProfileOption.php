<?php

namespace App\Models;

use App\Enums\Profile\ProfileOptionTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileOption extends Model
{
    use HasFactory;

    protected $table = 'profile_options';

    protected $fillable = [
        'type',
        'category',
        'name',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'type' => ProfileOptionTypeEnum::class,
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Faol parametrlar scope.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Bo'lim turi bo'yicha filter scope.
     */
    public function scopeType(Builder $query, string|ProfileOptionTypeEnum $type): Builder
    {
        $val = $type instanceof ProfileOptionTypeEnum ? $type->value : $type;
        return $query->where('type', $val);
    }

    /**
     * Tartib bo'yicha saralash scope.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Ikonka va nom birga formatlangan sarlavha.
     */
    public function getFormattedTitleAttribute(): string
    {
        return $this->icon ? "{$this->icon} {$this->name}" : $this->name;
    }
}
