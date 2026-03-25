<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->requiresAccountSetupCompletion() && ! $request->routeIs('patient.complete-profile.*')) {
            return redirect()->route('patient.complete-profile.show');
        }

        return $next($request);
    }
}
