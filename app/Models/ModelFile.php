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

        // Full remote URL (e.g. Unsplash, external CDN)
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            $parsed = parse_url($this->file_path);
            if (isset($parsed['host']) && in_array($parsed['host'], ['localhost', '127.0.0.1'])) {
                return $parsed['path'] ?? $this->file_path;
            }
            return $this->file_path;
        }

        $cleanPath = ltrim($this->file_path, '/');

        // If it starts with storage/
        if (str_starts_with($cleanPath, 'storage/')) {
            return '/' . $cleanPath;
        }

        // If it starts with assets/
        if (str_starts_with($cleanPath, 'assets/')) {
            return '/' . $cleanPath;
        }

        // Local storage files (e.g. photos/..., receipts/...)
        return '/storage/' . $cleanPath;
    }
}
