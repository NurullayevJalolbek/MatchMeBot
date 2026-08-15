<?php

namespace App\Services;

use App\Contracts\iDiscoveryService;
use App\Models\User;
use App\Models\UserFilter;

class DiscoveryService implements iDiscoveryService
{
    /**
     * Get or create discovery filter preferences for user.
     */
    public function getFilter(User $user): UserFilter
    {
        return UserFilter::firstOrCreate(
            ['user_id' => $user->id],
            [
                'looking_for' => $user->looking_for ?? 'female',
                'min_age' => 18,
                'max_age' => 28,
                'max_distance_km' => 50,
                'city' => $user->city ?? 'all',
            ]
        );
    }

    /**
     * Update user discovery filter preferences.
     */
    public function saveFilter(User $user, array $filterData): UserFilter
    {
        $filter = $this->getFilter($user);

        $filter->update([
            'looking_for' => $filterData['looking_for'] ?? $filter->looking_for,
            'min_age' => (int) ($filterData['min_age'] ?? $filter->min_age),
            'max_age' => (int) ($filterData['max_age'] ?? $filter->max_age),
            'max_distance_km' => (int) ($filterData['max_distance_km'] ?? $filter->max_distance_km),
            'city' => $filterData['city'] ?? $filter->city,
        ]);

        return $filter->fresh();
    }

    /**
     * Fetch matching candidate cards based on user filters.
     */
    public function getCandidates(User $user, int $limit = 20): array
    {
        $filter = $this->getFilter($user);

        $query = User::where('id', '!=', $user->id)
            ->where('onboarding_completed', true)
            ->with(['photos', 'primaryPhoto']);

        if ($filter->looking_for !== 'all') {
            $query->where('gender', $filter->looking_for);
        }

        if (!empty($filter->min_age)) {
            $query->where('age', '>=', $filter->min_age);
        }

        if (!empty($filter->max_age)) {
            $query->where('age', '<=', $filter->max_age);
        }

        if (!empty($filter->city) && $filter->city !== 'all') {
            $query->where('city', $filter->city);
        }

        return $query->limit($limit)->get()->toArray();
    }
}
