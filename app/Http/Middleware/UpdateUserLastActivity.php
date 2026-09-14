<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $user->last_activity_at = now();
            $user->saveQuietly();
        }

        return $next($request);
    }
}
