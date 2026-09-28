<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!auth()->user()->hasRole($role)) {
            return redirect()->route('home')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
