<?php

namespace App\Services;

use App\Contracts\iSubscriptionFeatureService;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\SubscriptionFeature;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class SubscriptionFeatureService implements iSubscriptionFeatureService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = SubscriptionFeature::class;

    /**
     * Model uchun query builder.
     */
    protected function query(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Paginate subscription features for admin list.
     */
    public function paginateSubscriptionFeatures(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('order', 'asc')->orderBy('id', 'asc')->paginate($perPage);
    }

    /**
     * Create a new feature with optional uploaded icon file.
     */
    public function createSubscriptionFeature(array $data, ?UploadedFile $iconFile = null): SubscriptionFeature
    {
        if ($iconFile) {
            $data['icon'] = $this->uploadIcon($iconFile);
        }

        $data = $this->prepareData($data);

        return $this->modelClass::create($data);
    }

    /**
     * Update an existing feature with optional new uploaded icon file.
     */
    public function updateSubscriptionFeature(SubscriptionFeature $feature, array $data, ?UploadedFile $iconFile = null): SubscriptionFeature
    {
        if ($iconFile) {
            $this->deleteIconFile($feature->icon);
            $data['icon'] = $this->uploadIcon($iconFile);
        }

        $data = $this->prepareData($data);

        $feature->update($data);

        return $feature;
    }

    /**
     * Delete a feature and its associated icon file.
     */
    public function deleteSubscriptionFeature(SubscriptionFeature $feature): bool
    {
        $this->deleteIconFile($feature->icon);

        return (bool) $feature->delete();
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleSubscriptionFeatureStatus(SubscriptionFeature $feature): SubscriptionFeature
    {
        $currentStatus = $feature->status instanceof SubscriptionStatusEnum ? $feature->status->value : ($feature->status ?: 'active');
        $newStatus = ($currentStatus === SubscriptionStatusEnum::ACTIVE->value) ? SubscriptionStatusEnum::INACTIVE : SubscriptionStatusEnum::ACTIVE;

        $feature->update([
            'status' => $newStatus,
            'is_active' => $newStatus === SubscriptionStatusEnum::ACTIVE,
        ]);

        return $feature;
    }

    /**
     * Get all active features for users.
     */
    public function getActiveFeatures(): Collection
    {
        return $this->query()
            ->active()
            ->get();
    }

    /**
     * Upload icon file to public/uploads/features directory.
     */
    protected function uploadIcon(UploadedFile $file): string
    {
        $directory = public_path('uploads/features');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $filename = 'feature_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/features/' . $filename;
    }

    /**
     * Delete existing icon file if on local filesystem.
     */
    protected function deleteIconFile(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * Clean and prepare data attributes before saving.
     */
    protected function prepareData(array $data): array
    {
        $statusValue = $data['status'] instanceof SubscriptionStatusEnum ? $data['status']->value : ($data['status'] ?? 'active');
        $data['status'] = $statusValue;
        $data['is_active'] = $statusValue === SubscriptionStatusEnum::ACTIVE->value;
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
