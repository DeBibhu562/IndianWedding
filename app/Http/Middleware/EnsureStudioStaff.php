<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudioStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        if (! Tenant::id()) {
            Tenant::set(Tenant::soleOrganizationId());
        }

        $role = $user->roleIn(Tenant::current());

        if ($role === Role::Client) {
            return redirect()->route('client.dashboard');
        }

        $membership = $user->membershipIn(Tenant::current());
        abort_unless(
            $user->hasFullStudioAccess()
            || ($membership?->isStudioStaff() ?? false),
            403,
        );

        return $next($request);
    }
}
