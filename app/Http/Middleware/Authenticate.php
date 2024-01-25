<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // check if request is from API
        if ($guards[0] == 'sanctum') {

            // check if user's role is allowed to access this route
            if ($guards[1] != auth()->user()->role) {
                return response()->json([
                    'message' => 'This route is for role: ' . $guards[1]
                ], 403);
            }
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
