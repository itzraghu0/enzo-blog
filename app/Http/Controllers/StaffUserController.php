<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertStaffUserRequest;
use App\Models\User;
use App\Services\StaffUserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffUserController extends Controller
{
    public function __construct(private readonly StaffUserService $staffUserService)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => is_numeric($request->query('role')) ? (int) $request->query('role') : null,
            'verified' => $request->has('verified') ? $request->boolean('verified') : null,
        ];

        $perPage = (int) $request->query('per_page', 20);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }

        return view('admin.staff.index', [
            'staff' => $this->staffUserService->paginate($filters, $perPage),
            'stats' => $this->staffUserService->summary(),
            'roleOptions' => $this->staffUserService->roles(),
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'perPageOptions' => [10, 20, 50],
        ]);
    }

    public function create(): View
    {
        return view('admin.staff.create', [
            'staff' => new User(),
            'roleOptions' => $this->staffUserService->roles(),
        ]);
    }

    public function store(UpsertStaffUserRequest $request): RedirectResponse
    {
        $user = $this->staffUserService->create($request->validated());

        return redirect()
            ->route('admin.staff.edit', $user)
            ->with('success', __('Staff user created successfully'));
    }

    public function edit(User $staff): View
    {
        abort_unless($staff->isAdmin() || $staff->isEditor() || $staff->isAuthor(), 404);

        return view('admin.staff.edit', [
            'staff' => $staff,
            'roleOptions' => $this->staffUserService->roles(),
        ]);
    }

    public function update(UpsertStaffUserRequest $request, User $staff): RedirectResponse
    {
        abort_unless($staff->isAdmin() || $staff->isEditor() || $staff->isAuthor(), 404);

        $this->staffUserService->update($staff, $request->validated());

        return redirect()
            ->route('admin.staff.edit', $staff)
            ->with('success', __('Staff user updated successfully'));
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        abort_unless($staff->isAdmin() || $staff->isEditor() || $staff->isAuthor(), 404);

        if ($request->user()?->is($staff)) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        $this->staffUserService->delete($staff);

        return back()->with('success', __('Staff user deleted successfully'));
    }
}
