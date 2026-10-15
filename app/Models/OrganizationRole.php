<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrganizationRole extends Model
{
    use BelongsToOrganization;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OrganizationRole $role): void {
            $role->slug ??= Str::slug($role->name);
        });
    }

    public function storageKey(): string
    {
        return OrganizationUser::customRoleKey($this->slug);
    }

    /**
     * @return list<string>
     */
    public function permissionList(): array
    {
        $permissions = $this->permissions ?? [];

        return is_array($permissions) ? array_values(array_filter($permissions, 'is_string')) : [];
    }
}
