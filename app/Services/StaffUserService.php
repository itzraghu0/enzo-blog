<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffUserService
{
    public function summary(): array
    {
        return [
            'total' => User::query()->whereIn('role', $this->staffRoles())->count(),
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'editors' => User::query()->where('role', User::ROLE_EDITOR)->count(),
            'authors' => User::query()->where('role', User::ROLE_AUTHOR)->count(),
        ];
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $role = $filters['role'] ?? null;
        $verified = $filters['verified'] ?? null;

        return User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at'])
            ->whereIn('role', $this->staffRoles())
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
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function roles(): Collection
    {
        return collect([
            User::ROLE_ADMIN => __('Admin'),
            User::ROLE_EDITOR => __('Editor'),
            User::ROLE_AUTHOR => __('Author'),
        ]);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            return tap(User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => (int) $data['role'],
                'email_verified_at' => now(),
            ]));
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => (int) $data['role'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            if (array_key_exists('email_verified_at', $data)) {
                $payload['email_verified_at'] = $data['email_verified_at'] ? now() : null;
            }

            $user->update($payload);

            return $user->refresh();
        });
    }

    public function delete(User $user): bool
    {
        return (bool) DB::transaction(function () use ($user): bool {
            return (bool) $user->delete();
        });
    }

    public function staffRoles(): array
    {
        return [
            User::ROLE_ADMIN,
            User::ROLE_EDITOR,
            User::ROLE_AUTHOR,
        ];
    }
}
