<?php

namespace App\Support;

use App\Enums\Role;
use App\Models\OrganizationRole;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class RoleCatalog
{
    public const CUSTOM_PREFIX = 'custom:';

    /**
     * @return list<array{value: string, label: string, description: ?string, custom: bool}>
     */
    public static function optionsForActor(?User $user = null): array
    {
        $user ??= auth()->user();
        if (! $user) {
            return [];
        }

        $options = [];

        foreach (AssignableTeamRoles::forActor($user) as $role) {
            $options[] = [
                'value' => $role->value,
                'label' => $role->label(),
                'description' => null,
                'custom' => false,
            ];
        }

        $orgId = Tenant::id();
        if (! $orgId || ! Schema::hasTable('organization_roles')) {
            return $options;
        }

        foreach (OrganizationRole::withoutTenant()->where('organization_id', $orgId)->orderBy('sort_order')->orderBy('name')->get() as $custom) {
            $options[] = [
                'value' => $custom->storageKey(),
                'label' => $custom->name,
                'description' => $custom->description,
                'custom' => true,
            ];
        }

        return $options;
    }

    public static function canAssign(?User $actor, string $roleValue): bool
    {
        if (str_starts_with($roleValue, self::CUSTOM_PREFIX)) {
            if (! self::canManageCustomRoles($actor) && ! $actor?->can('create', \App\Models\OrganizationUser::class)) {
                return OrganizationRole::withoutTenant()
                    ->where('organization_id', Tenant::id())
                    ->get()
                    ->contains(fn (OrganizationRole $role) => $role->storageKey() === $roleValue);
            }

            return self::canManageCustomRoles($actor)
                || (bool) $actor?->can('create', \App\Models\OrganizationUser::class);
        }

        $enum = Role::tryFrom($roleValue);

        return $enum && AssignableTeamRoles::canAssign($actor, $enum);
    }

    public static function canManageCustomRoles(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user?->hasFullStudioAccess()
            || $user?->canInOrganization('permissions.manage', Tenant::current())
            || $user?->canInOrganization('users.manage', Tenant::current());
    }

    /**
     * @return list<string>
     */
    public static function reservedSlugs(): array
    {
        return array_map(fn (Role $role) => $role->value, Role::cases());
    }
}
