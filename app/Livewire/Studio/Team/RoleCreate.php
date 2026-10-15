<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Permission;
use App\Livewire\Studio\Team\Concerns\ManagesCustomRoleForm;
use App\Models\OrganizationRole;
use App\Support\RoleCatalog;
use App\Support\Tenant;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Create role')]
class RoleCreate extends Component
{
    use ManagesCustomRoleForm;

    public string $name = '';

    public string $description = '';

    public function mount(): void
    {
        abort_unless(RoleCatalog::canManageCustomRoles(), 403);

        $this->initPermissionStates();
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

        if (OrganizationRole::withoutTenant()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $this->addError('name', 'A role with this name already exists.');

            return;
        }

        $permissions = $this->selectedPermissions();

        $sort = (int) OrganizationRole::withoutTenant()->where('organization_id', $orgId)->max('sort_order');

        OrganizationRole::withoutTenant()->create([
            'organization_id' => $orgId,
            'name' => $data['name'],
            'slug' => $slug,
            'description' => trim($this->description) ?: null,
            'permissions' => $permissions,
            'sort_order' => $sort + 1,
        ]);

        session()->flash('status', 'Role "'.$data['name'].'" created. You can assign it when adding team members.');

        $this->redirect(route('app.team.roles'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.team.role-create', [
            'groups' => Permission::grouped(),
        ]);
    }
}
