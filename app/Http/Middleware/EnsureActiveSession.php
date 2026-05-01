<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $timeout = (int) config('session.lifetime', 60) * 60;
        $lastActivity = (int) $request->session()->get('last_activity_at', now()->timestamp);

        if (now()->timestamp - $lastActivity >= $timeout) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'La sesión expiró por inactividad.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors(['login_error' => 'La sesión expiró por inactividad. Inicia sesión nuevamente.']);
        }

        $request->session()->put('last_activity_at', now()->timestamp);

        return $next($request);
    }
}
