<?php

namespace App\Support;

use App\Enums\Role;
use App\Models\User;

class AssignableTeamRoles
{
    /**
     * Roles the acting user may assign when adding or editing a team member.
     *
     * @return list<Role>
     */
    public static function forActor(?User $user = null): array
    {
        $user ??= auth()->user();
        if (! $user) {
            return [];
        }

        $actorRole = $user->roleIn(Tenant::current());

        if ($user->is_super_admin || $actorRole === Role::StudioAdmin || $user->isOrganizationOwner(Tenant::current())) {
            return array_values(array_filter(
                Role::cases(),
                fn (Role $role) => $role->isStaff() && $role !== Role::Client,
            ));
        }

        if ($actorRole === Role::Admin) {
            return [
                Role::Admin,
                Role::Manager,
                Role::Photographer,
                Role::Videographer,
                Role::Editor,
            ];
        }

        if ($user->canInOrganization('users.manage', Tenant::current())) {
            return [
                Role::Manager,
                Role::Photographer,
                Role::Videographer,
                Role::Editor,
            ];
        }

        return [];
    }

    public static function canAssign(?User $actor, Role $role): bool
    {
        foreach (self::forActor($actor) as $allowed) {
            if ($allowed === $role) {
                return true;
            }
        }

        return false;
    }
}
