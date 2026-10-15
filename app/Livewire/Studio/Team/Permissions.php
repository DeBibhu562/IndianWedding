<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\OrganizationUser;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Team permissions')]
class Permissions extends Component
{
    public ?int $selectedMemberId = null;

    /** @var array<string, bool> */
    public array $permissionStates = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->canInOrganization('permissions.manage', Tenant::current()), 403);
    }

    public function selectMember(int $memberId): void
    {
        $member = $this->findMember($memberId);
        $this->selectedMemberId = $member->id;
        $this->loadPermissionStates($member);
    }

    public function savePermissions(): void
    {
        abort_unless(auth()->user()?->canInOrganization('permissions.manage', Tenant::current()), 403);

        $member = $this->findMember($this->selectedMemberId);
        abort_if(
            in_array($member->roleEnum(), [Role::StudioAdmin, Role::Admin], true),
            403,
            'Studio Admin and Admin roles are managed by the system.',
        );

        $roleDefaults = $member->rolePermissions();
        $grants = [];
        $revokes = [];

        foreach (Permission::grouped() as $permissions) {
            foreach ($permissions as $permission) {
                $key = $permission->value;
                $enabled = (bool) ($this->permissionStates[$key] ?? false);
                $inRole = in_array($key, $roleDefaults, true);

                if ($enabled && ! $inRole) {
                    $grants[] = $key;
                }

                if (! $enabled && $inRole) {
                    $revokes[] = $key;
                }
            }
        }

        $member->update([
            'permissions' => [
                'grants' => array_values(array_unique($grants)),
                'revokes' => array_values(array_unique($revokes)),
            ],
        ]);

        session()->flash('status', 'Permissions updated for '.$member->user?->name.'.');
    }

    protected function findMember(?int $memberId): OrganizationUser
    {
        abort_unless($memberId, 404);

        return OrganizationUser::query()
            ->with('user')
            ->where('organization_id', Tenant::id())
            ->whereKey($memberId)
            ->whereNot('role', Role::Client->value)
            ->firstOrFail();
    }

    protected function loadPermissionStates(OrganizationUser $member): void
    {
        $custom = is_array($member->permissions) ? $member->permissions : [];
        $grants = $custom['grants'] ?? [];
        $revokes = $custom['revokes'] ?? [];
        $roleDefaults = $member->rolePermissions();

        $this->permissionStates = [];

        foreach (Permission::grouped() as $permissions) {
            foreach ($permissions as $permission) {
                $key = $permission->value;
                $inRole = in_array($key, $roleDefaults, true);
                $granted = in_array($key, $grants, true);
                $revoked = in_array($key, $revokes, true);

                $this->permissionStates[$key] = ($inRole && ! $revoked) || $granted;
            }
        }
    }

    public function render()
    {
        $members = OrganizationUser::query()
            ->with('user')
            ->where('organization_id', Tenant::id())
            ->whereNot('role', Role::Client->value)
            ->orderByRaw("CASE role WHEN 'studio_admin' THEN 0 WHEN 'manager' THEN 1 ELSE 2 END")
            ->get();

        $selected = $members->firstWhere('id', $this->selectedMemberId);

        return view('livewire.studio.team.permissions', [
            'members' => $members,
            'selected' => $selected,
            'groups' => Permission::grouped(),
        ]);
    }
}
