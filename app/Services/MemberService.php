<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MemberService
{
    public function summary(): array
    {
        return [
            'total' => User::query()->where('role', User::ROLE_VIEWER)->count(),
            'verified' => User::query()->where('role', User::ROLE_VIEWER)->whereNotNull('email_verified_at')->count(),
            'pending' => User::query()->where('role', User::ROLE_VIEWER)->whereNull('email_verified_at')->count(),
        ];
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $verified = $filters['verified'] ?? null;

        return User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at'])
            ->where('role', User::ROLE_VIEWER)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
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
}
