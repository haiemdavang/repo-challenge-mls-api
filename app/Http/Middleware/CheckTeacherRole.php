<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeacherRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow Manager or Teacher
        if (!$user || ($user->role_id !== Role::TEACHER && $user->role_id !== Role::MANAGER)) {
            throw new ForbiddenException('TEACHER_ACCESS_REQUIRED');
        }

        return $next($request);
    }
}
