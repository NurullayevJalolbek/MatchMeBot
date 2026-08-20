<?php

namespace App\Contracts;

use App\Models\SubscriptionFeature;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface iSubscriptionFeatureService
{
    /**
     * Paginate subscription features for admin list.
     */
    public function paginateSubscriptionFeatures(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new feature with optional uploaded icon file.
     */
    public function createSubscriptionFeature(array $data, ?UploadedFile $iconFile = null): SubscriptionFeature;

    /**
     * Update an existing feature with optional new uploaded icon file.
     */
    public function updateSubscriptionFeature(SubscriptionFeature $feature, array $data, ?UploadedFile $iconFile = null): SubscriptionFeature;

    /**
     * Delete a feature and its associated icon file.
     */
    public function deleteSubscriptionFeature(SubscriptionFeature $feature): bool;

    /**
     * Toggle status between active and inactive.
     */
    public function toggleSubscriptionFeatureStatus(SubscriptionFeature $feature): SubscriptionFeature;

    /**
     * Get all active features for users.
     */
    public function getActiveFeatures(): Collection;
}
