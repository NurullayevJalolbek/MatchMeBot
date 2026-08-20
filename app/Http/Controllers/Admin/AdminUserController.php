<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iAdminUserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserStoreRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        protected iAdminUserService $adminService
    ) {}

    /**
     * Display a listing of the administrators.
     */
    public function index(Request $request): View
    {
        $admins = $this->adminService->paginateAdmins($request->only(['search', 'status']), 10);

        return view('admin.pages.admins.index', [
            'datas' => $admins,
            'admins' => $admins,
        ]);
    }

    /**
     * Show the form for creating a new administrator.
     */
    public function create(): View
    {
        return view('admin.pages.admins.create');
    }

    /**
     * Store a newly created administrator in storage.
     */
    public function store(AdminUserStoreRequest $request): RedirectResponse
    {
        $this->adminService->createAdmin($request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', 'Yangi Administrator muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Show the form for editing the specified administrator.
     */
    public function edit(User $admin): View
    {
        return view('admin.pages.admins.edit', [
            'model' => $admin,
            'admin' => $admin,
        ]);
    }

    /**
     * Update the specified administrator in storage.
     */
    public function update(AdminUserUpdateRequest $request, User $admin): RedirectResponse
    {
        $this->adminService->updateAdmin($admin, $request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrator ma\'lumotlari muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified administrator from storage.
     */
    public function destroy(Request $request, User $admin)
    {
        try {
            $this->adminService->deleteAdmin($admin);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Administrator muvaffaqiyatli o\'chirildi!',
                ]);
            }

            return redirect()->route('admin.admins.index')
                ->with('success', 'Administrator muvaffaqiyatli o\'chirildi!');
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->first('admin') ?: 'O\'chirishda xatolik yuz berdi!';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->route('admin.admins.index')->with('error', $message);
        }
    }

    /**
     * Toggle administrator status between active and blocked.
     */
    public function toggleStatus(User $admin): RedirectResponse
    {
        try {
            $this->adminService->toggleAdminStatus($admin);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Administrator statusi muvaffaqiyatli o\'zgartirildi!');
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->first('admin') ?: 'Statusni o\'zgartirishda xatolik!';

            return redirect()->route('admin.admins.index')->with('error', $message);
        }
    }
}
