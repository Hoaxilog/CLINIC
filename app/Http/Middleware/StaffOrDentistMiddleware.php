<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StaffOrDentistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $role = Auth::user()?->role;
        $isStaff = in_array($role, [1, 2], true);

        if (!$isStaff) {
            if ($role === 3) {
                return redirect()->route('patient.dashboard');
            }
            return abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
