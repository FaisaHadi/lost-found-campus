<?php

namespace App\Http\Middleware;

use BackedEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if (! $request->expectsJson()) {
                return redirect()->route('admin.login');
            }

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Authentication token is missing or invalid.',
                ],
            ], 401);
        }

        $role = $user->role instanceof BackedEnum
            ? $user->role->value
            : $user->role;

        if (! in_array($role, $roles, true)) {
            if (! $request->expectsJson()) {
                abort(403);
            }

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'The authenticated user does not have the required role.',
                ],
            ], 403);
        }

        return $next($request);
    }
}
