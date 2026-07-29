<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(Request $request): View
    {
        $verified = $request->boolean('verified');
        $role = $request->query('role');
        $role = is_numeric($role) ? (int) $role : null;

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => $role,
            'verified' => $request->has('verified') ? $verified : null,
        ];

        $perPage = (int) $request->query('per_page', 20);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }

        return view('admin.users.index', [
            'users' => $this->userService->paginate($filters, $perPage),
            'stats' => $this->userService->summary(),
            'roleOptions' => $this->userService->roles(),
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'perPageOptions' => [10, 20, 50],
        ]);
    }
}
