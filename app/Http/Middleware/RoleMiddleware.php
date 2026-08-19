<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Allow access only when the authenticated user's role matches one of the allowed roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = strtolower(trim((string) $request->user()?->role));
        $allowedRoles = array_map(
            static fn (string $role): string => strtolower(trim($role)),
            $roles
        );

        abort_unless($userRole !== '' && in_array($userRole, $allowedRoles, true), 403);

        return $next($request);
    }
}
