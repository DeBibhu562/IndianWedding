<?php

namespace App\Livewire\Studio\Team\Concerns;

use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Support\Arr;

trait ManagesCustomRoleForm
{
    /** @var array<string, bool> */
    public array $permissionStates = [];

    /**
     * @param  list<string>  $enabled
     */
    protected function initPermissionStates(array $enabled = []): void
    {
        foreach (Permission::grouped() as $permissions) {
            foreach ($permissions as $permission) {
                $this->permissionStates[$permission->value] = in_array($permission->value, $enabled, true);
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
}
