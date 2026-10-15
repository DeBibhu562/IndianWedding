<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\OrganizationRole;
use App\Support\RoleCatalog;
use App\Support\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Create role')]
class RoleCreate extends Component
{
    public string $name = '';

    public string $description = '';

    /** @var array<string, bool> */
    public array $permissionStates = [];

    public function mount(): void
    {
        abort_unless(RoleCatalog::canManageCustomRoles(), 403);

        foreach (Permission::grouped() as $permissions) {
            foreach ($permissions as $permission) {
                $this->permissionStates[$permission->value] = false;
            }
        }
    }

    public function applyTemplate(string $template): void
    {
        $defaults = match ($template) {
            'photographer' => Role::Photographer->permissions(),
            'manager' => Role::Manager->permissions(),
            'editor' => Role::Editor->permissions(),
            default => [],
        };

        foreach (Permission::cases() as $permission) {
            $this->permissionStates[$permission->value] = in_array($permission->value, $defaults, true);
        }
    }

    /**
     * @return list<string>
     */
    protected function selectedPermissions(): array
    {
        $flat = Arr::dot($this->permissionStates);

        return collect(Permission::cases())
            ->filter(fn (Permission $permission) => filter_var($flat[$permission->value] ?? false, FILTER_VALIDATE_BOOLEAN))
            ->map(fn (Permission $permission) => $permission->value)
            ->values()
            ->all();
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
