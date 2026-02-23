<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsPatientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $role = Auth::user()?->role;
        $isPatient = $role === 3;

        if (!$isPatient) {
            if (in_array($role, [1, 2], true)) {
                return redirect()->route('dashboard');
            }
            return abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
