<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ModelFile extends Model
{
    use HasFactory;

    protected $table = 'model_files';

    protected $fillable = [
        'model_type',
        'model_id',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'order',
        'is_main',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_main' => 'boolean',
        'file_size' => 'integer',
    ];

    protected $appends = [
        'url',
    ];

    /**
     * Get the owning model.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get public URL attribute for the file.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '';
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return '/' . ltrim($this->file_path, '/');
    }
}
