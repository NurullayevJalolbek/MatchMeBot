<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface iAdminUserService
{
    /**
     * Paginate admin users for management list.
     */
    public function paginateAdmins(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new administrator user and attach admin role.
     */
    public function createAdmin(array $data): User;

    /**
     * Update an existing administrator user.
     */
    public function updateAdmin(User $admin, array $data): User;

    /**
     * Delete an administrator user.
     */
    public function deleteAdmin(User $admin): bool;

    /**
     * Toggle admin status between active and blocked.
     */
    public function toggleAdminStatus(User $admin): User;
}
