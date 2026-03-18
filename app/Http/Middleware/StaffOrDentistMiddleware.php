<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffOrDentistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $user = Auth::user();
        $role = (int) ($user?->role ?? 0);
        $isStaff = $user?->canAccessOperationalPages() ?? false;

        if (! $isStaff) {
            if ($role === User::ROLE_PATIENT) {
                return redirect()->route('patient.dashboard');
            }

            return abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
