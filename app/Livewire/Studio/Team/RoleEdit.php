<?php

namespace App\Livewire\Studio\Team;

use App\Livewire\Studio\Team\Concerns\ManagesCustomRoleForm;
use App\Models\OrganizationRole;
use App\Models\OrganizationUser;
use App\Support\RoleCatalog;
use App\Support\Tenant;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit role')]
class RoleEdit extends Component
{
    use ManagesCustomRoleForm;

    public OrganizationRole $role;

    public string $name = '';

    public string $description = '';

    public function mount(OrganizationRole $role): void
    {
        abort_unless(RoleCatalog::canManageCustomRoles(), 403);

        $this->role = $role;
        $this->name = $role->name;
        $this->description = $role->description ?? '';
        $this->initPermissionStates($role->permissionList());
    }

    public function save(): void
    {
        abort_unless(RoleCatalog::canManageCustomRoles(), 403);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = Str::slug($data['name']);
        abort_if($slug === '', 422, 'Choose a role name that produces a valid slug.');

        if (in_array($slug, RoleCatalog::reservedSlugs(), true)) {
            $this->addError('name', 'This name conflicts with a built-in role. Choose another name.');

            return;
        }

        $orgId = Tenant::requireId();

        if (OrganizationRole::withoutTenant()
            ->where('organization_id', $orgId)
            ->where('slug', $slug)
            ->whereKeyNot($this->role->id)
            ->exists()) {
            $this->addError('name', 'A role with this name already exists.');

            return;
        }

        $previousKey = $this->role->storageKey();
        $permissions = $this->selectedPermissions();

        $this->role->update([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => trim($this->description) ?: null,
            'permissions' => $permissions,
        ]);

        $this->role->refresh();

        if ($previousKey !== $this->role->storageKey()) {
            OrganizationUser::query()
                ->where('organization_id', $orgId)
                ->where('role', $previousKey)
                ->update(['role' => $this->role->storageKey()]);
        }

        session()->flash('status', 'Role "'.$data['name'].'" updated.');

        $this->redirect(route('app.team.roles'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.team.role-edit', [
            'groups' => \App\Enums\Permission::grouped(),
        ]);
    }
}
