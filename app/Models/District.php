<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'name_uz',
        'name_ru',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'region_id' => 'integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Region this district belongs to.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
