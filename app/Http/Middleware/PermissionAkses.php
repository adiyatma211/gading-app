<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionAkses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Pastikan user punya role_id dan bisa mengakses relasi
        if (!$user->role || !in_array($user->role->rolesName, $allowedRoles)) {
            return response()->view('patrial.error.v_forbidden', [], 403);
        }

        return $next($request);
    }
}
