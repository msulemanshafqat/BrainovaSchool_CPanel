<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks users with the Teacher role (role_id = 5) from admin-only homework routes
 * (e.g. global homework overview) regardless of homework_read.
 */
class DenyTeacherRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (int) Auth::user()->role_id === 5) {
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
