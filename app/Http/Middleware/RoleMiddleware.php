<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        \Log::info('RoleMiddleware check', [
            'user_id' => $user ? $user->id : null,
            'user_role' => $user ? $user->role : null,
            'required_role' => $role,
            'authenticated' => Auth::check(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        if (!Auth::check() || Auth::user()->role !== $role) {
            \Log::warning('RoleMiddleware access denied', [
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : null,
                'required_role' => $role
            ]);
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
