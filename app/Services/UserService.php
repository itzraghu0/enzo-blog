<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    public function summary(): array
    {
        return [
            'total' => User::query()->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'authors' => User::query()->whereIn('role', [User::ROLE_EDITOR, User::ROLE_AUTHOR])->count(),
            'viewers' => User::query()->where('role', User::ROLE_VIEWER)->count(),
        ];
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $role = $filters['role'] ?? null;
        $verified = $filters['verified'] ?? null;

        $query = User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($role !== null && $role !== '', function ($query) use ($role): void {
                $query->where('role', (int) $role);
            })
            ->when($verified !== null, function ($query) use ($verified): void {
                if ((bool) $verified) {
                    $query->whereNotNull('email_verified_at');
                }
            })
            ->latest('created_at');

        return $query->paginate($perPage)->withQueryString();
    }

    public function roles(): Collection
    {
        return collect([
            User::ROLE_ADMIN => __('Admin'),
            User::ROLE_EDITOR => __('Editor'),
            User::ROLE_AUTHOR => __('Author'),
            User::ROLE_VIEWER => __('Viewer'),
        ]);
    }
}
