<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // converte string recebida em Enum
        $requiredRole = UserRole::from($role);

        if ($user->role !== $requiredRole) {
            abort(403);
        }

        return $next($request);
    }
}