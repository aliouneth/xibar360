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

        $user = auth()->user();

        // Deactivation takes effect immediately, even on a still-valid
        // session: without this, toggling is_active off would not lock
        // anybody out of the back office.
        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('login')->with('error', 'Ce compte a été désactivé.');
        }

        // Route parameters arrive as one string ("admin|editor"), so split
        // them; passing the raw string would match no role at all.
        if (! $user->hasRole(explode('|', $role))) {
            return redirect()->route('home')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
