<?php

namespace App\Http\Controllers;

use App\Services\MemberService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(private readonly MemberService $memberService)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'verified' => $request->has('verified') ? $request->boolean('verified') : null,
        ];

        $perPage = (int) $request->query('per_page', 20);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }

        return view('admin.members.index', [
            'members' => $this->memberService->paginate($filters, $perPage),
            'stats' => $this->memberService->summary(),
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'perPageOptions' => [10, 20, 50],
        ]);
    }
}
