<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user?->canManageBlog()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
