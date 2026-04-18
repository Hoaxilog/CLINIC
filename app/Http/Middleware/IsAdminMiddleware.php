<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $user = Auth::user();
        $role = (int) ($user?->role ?? 0);
        $isAdmin = $user?->isAdmin() ?? false;

        if (! $isAdmin) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
