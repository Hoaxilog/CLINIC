<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You must log in first.']);
        }

        $userId = Auth::id();
        $role = DB::table('users')->where('id', $userId)->value('role');

        $isAdmin = $role === 1;

        if (!$isAdmin) {
            if ($role === 3) {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
