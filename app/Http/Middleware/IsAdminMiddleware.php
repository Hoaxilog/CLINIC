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

        $isAdmin = $role === 1 || $role === 4;

        if (!$isAdmin) {
            return abort(403, 'Unauthorized.');
            // or: return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
