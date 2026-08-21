<?php

namespace App\Contracts;

use App\Models\ProfileOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface iProfileOptionService
{
    /**
     * Get paginated profile options with optional filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active options grouped by category for a specific type.
     */
    public function getGroupedByType(string $type): Collection;

    /**
     * Store a newly created profile option.
     */
    public function create(array $data): ProfileOption;

    /**
     * Update the specified profile option.
     */
    public function update(ProfileOption $profileOption, array $data): ProfileOption;

    /**
     * Toggle active status.
     */
    public function toggleStatus(ProfileOption $profileOption): bool;

    /**
     * Delete the specified profile option.
     */
    public function delete(ProfileOption $profileOption): bool;
}
