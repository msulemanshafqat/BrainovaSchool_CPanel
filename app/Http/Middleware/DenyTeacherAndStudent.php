<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks school portal users with Teacher, Student, or Parent/Guardian roles from
 * CMS / system configuration routes (website setup, languages, app settings, notifications).
 * Uses role slug so it stays correct if role IDs differ per tenant.
 */
class DenyTeacherAndStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('role');
            $slug = $user->role?->slug;
            if ($slug !== null && $slug !== '' && in_array($slug, schoolSettingsBlockedRoleSlugs(), true)) {
                abort(403, 'Access Denied');
            }
        }

        return $next($request);
    }
}
