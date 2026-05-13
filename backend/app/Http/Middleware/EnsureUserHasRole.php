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
            if (! $this->shouldReturnJson($request)) {
                return redirect()->route('admin.login');
            }

            return response()->json([
                'success' => false,
                'message' => 'Token autentikasi tidak ada atau tidak valid.',
                'data' => null,
                'errors' => null,
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Token autentikasi tidak ada atau tidak valid.',
                ],
                'meta' => [],
            ], 401);
        }

        $role = $user->role instanceof BackedEnum
            ? $user->role->value
            : $user->role;

        if (! in_array($role, $roles, true)) {
            if (! $this->shouldReturnJson($request)) {
                abort(403);
            }

            return response()->json([
                'success' => false,
                'message' => 'Pengguna yang masuk tidak memiliki peran yang diperlukan.',
                'data' => null,
                'errors' => null,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'Pengguna yang masuk tidak memiliki peran yang diperlukan.',
                ],
                'meta' => [],
            ], 403);
        }

        return $next($request);
    }

    private function shouldReturnJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
