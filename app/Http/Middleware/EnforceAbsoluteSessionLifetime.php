<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAbsoluteSessionLifetime
{
    private const SESSION_KEY = 'authenticated_at';
    private const MAX_AGE_SECONDS = 86400;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $authenticatedAt = (int) $request->session()->get(self::SESSION_KEY, 0);

            if ($authenticatedAt === 0) {
                $request->session()->put(self::SESSION_KEY, now()->timestamp);
            } elseif (now()->timestamp - $authenticatedAt >= self::MAX_AGE_SECONDS) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Sesi login Anda telah berakhir setelah 24 jam. Silakan masuk kembali.');
            }
        }

        return $next($request);
    }
}
