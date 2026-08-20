<?php

namespace App\Services;

use App\Contracts\iAdminUserService;
use App\Enums\Admin\AdminStatusEnum;
use App\Enums\User\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminUserService implements iAdminUserService
{
    /**
     * Asosiy model klassi.
     */
    protected string $modelClass = User::class;

    /**
     * Model query builder (Faqat role=admin bo'lganlar).
     */
    protected function query(): Builder
    {
        return $this->modelClass::query()->admins();
    }

    /**
     * Paginate admin users for management list.
     */
    public function paginateAdmins(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->query()->with('roles');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Create a new administrator user and attach admin role.
     */
    public function createAdmin(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['status'] = $data['status'] instanceof AdminStatusEnum ? $data['status']->value : ($data['status'] ?? 'active');

        /** @var User $user */
        $user = $this->modelClass::create($data);
        $user->assignRole(RoleEnum::ADMIN->value);

        return $user;
    }

    /**
     * Update an existing administrator user.
     */
    public function updateAdmin(User $admin, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['status'])) {
            $data['status'] = $data['status'] instanceof AdminStatusEnum ? $data['status']->value : $data['status'];
        }

        $admin->update($data);

        return $admin;
    }

    /**
     * Delete an administrator user (prevents self-deletion).
     */
    public function deleteAdmin(User $admin): bool
    {
        if (auth()->id() === $admin->id) {
            throw ValidationException::withMessages([
                'admin' => 'Siz o\'zingizning joriy akkauntingizni o\'chira olmaysiz!',
            ]);
        }

        return (bool) $admin->delete();
    }

    /**
     * Toggle admin status between active and blocked.
     */
    public function toggleAdminStatus(User $admin): User
    {
        if (auth()->id() === $admin->id) {
            throw ValidationException::withMessages([
                'admin' => 'Siz o\'zingizning akkauntingizni bloklay olmaysiz!',
            ]);
        }

        $currentStatus = $admin->status instanceof AdminStatusEnum ? $admin->status->value : ($admin->status ?: 'active');
        $newStatus = ($currentStatus === AdminStatusEnum::ACTIVE->value) ? AdminStatusEnum::BLOCKED : AdminStatusEnum::ACTIVE;

        $admin->update([
            'status' => $newStatus,
        ]);

        return $admin;
    }
}
