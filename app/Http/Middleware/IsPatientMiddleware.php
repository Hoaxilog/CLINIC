<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsPatientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $user = Auth::user();
        $role = (int) ($user?->role ?? 0);
        $isPatient = $user?->isPatient() ?? false;

        if (! $isPatient) {
            if (in_array($role, [
                User::ROLE_ADMIN,
                User::ROLE_DENTIST,
                User::ROLE_STAFF,
            ], true)) {
                return redirect()->route('dashboard');
            }

            return abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
