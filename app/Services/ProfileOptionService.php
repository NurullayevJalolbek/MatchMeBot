<?php

namespace App\Services;

use App\Contracts\iProfileOptionService;
use App\Models\ProfileOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProfileOptionService implements iProfileOptionService
{
    /**
     * Get paginated profile options with optional filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProfileOption::query();

        if (!empty($filters['type'])) {
            $query->type($filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('category', 'ilike', "%{$search}%")
                  ->orWhere('icon', 'ilike', "%{$search}%");
            });
        }

        return $query->orderBy('type', 'asc')
            ->orderBy('category', 'asc')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get active options grouped by category for a specific type.
     */
    public function getGroupedByType(string $type): Collection
    {
        return ProfileOption::active()
            ->type($type)
            ->ordered()
            ->get()
            ->groupBy('category');
    }

    /**
     * Store a newly created profile option.
     */
    public function create(array $data): ProfileOption
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
        $data['order'] = isset($data['order']) ? (int) $data['order'] : 0;

        return ProfileOption::create($data);
    }

    /**
     * Update the specified profile option.
     */
    public function update(ProfileOption $profileOption, array $data): ProfileOption
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['order'] = isset($data['order']) ? (int) $data['order'] : 0;

        $profileOption->update($data);
        return $profileOption;
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(ProfileOption $profileOption): bool
    {
        $profileOption->is_active = !$profileOption->is_active;
        return $profileOption->save();
    }

    /**
     * Delete the specified profile option.
     */
    public function delete(ProfileOption $profileOption): bool
    {
        return (bool) $profileOption->delete();
    }
}
