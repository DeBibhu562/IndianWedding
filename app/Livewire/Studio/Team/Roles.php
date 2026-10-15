<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationRole;
use App\Models\OrganizationUser;
use App\Support\AssignableTeamRoles;
use App\Support\RoleCatalog;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Roles')]
class Roles extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        abort_unless(
            $user?->can('create', OrganizationUser::class)
            || $user?->canInOrganization('permissions.manage', Tenant::current()),
            403,
        );
    }

    public function deleteRole(int $roleId): void
    {
        abort_unless(RoleCatalog::canManageCustomRoles(), 403);

        $role = OrganizationRole::withoutTenant()
            ->where('organization_id', Tenant::requireId())
            ->whereKey($roleId)
            ->firstOrFail();

        $inUse = OrganizationUser::query()
            ->where('organization_id', Tenant::requireId())
            ->where('role', $role->storageKey())
            ->exists();

        if ($inUse) {
            session()->flash('status', 'Cannot delete "'.$role->name.'" while team members still use it. Reassign them first.');

            return;
        }

        $role->delete();
        session()->flash('status', 'Role deleted.');
    }

    public function render()
    {
        $user = auth()->user();
        $assignable = AssignableTeamRoles::forActor($user);

        $descriptions = [
            Role::StudioAdmin->value => 'Full studio access, including permanent delete and all settings.',
            Role::Admin->value => 'Full operational access and team management. Cannot permanently delete records.',
            Role::Manager->value => 'Leads, clients, projects, calendar, and day-to-day operations.',
            Role::Photographer->value => 'Assigned shoots, file uploads, tasks, and calendar.',
            Role::Videographer->value => 'Same as photographer, focused on film deliverables.',
            Role::Editor->value => 'Post-production tasks, raw files, and uploads.',
        ];

        $customRoles = OrganizationRole::withoutTenant()
            ->where('organization_id', Tenant::id())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.studio.team.roles', [
            'assignable' => $assignable,
            'descriptions' => $descriptions,
            'customRoles' => $customRoles,
            'canAddMember' => $user->can('create', OrganizationUser::class),
            'canManagePermissions' => $user->canInOrganization('permissions.manage', Tenant::current()),
            'canManageCustomRoles' => RoleCatalog::canManageCustomRoles($user),
        ]);
    }
}
