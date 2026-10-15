<?php

namespace App\Models;

use App\Enums\Role;
use App\Support\RoleCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_owner' => 'boolean',
            'permissions' => 'array',
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public static function customRoleKey(string $slug): string
    {
        return RoleCatalog::CUSTOM_PREFIX.$slug;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roleEnum(): ?Role
    {
        if ($this->isCustomRole()) {
            return null;
        }

        $value = (string) $this->role;
        if ($value === 'super_admin') {
            $value = Role::StudioAdmin->value;
        }

        return Role::tryFrom($value);
    }

    public function isCustomRole(): bool
    {
        return str_starts_with((string) $this->role, RoleCatalog::CUSTOM_PREFIX);
    }

    public function customRoleSlug(): ?string
    {
        if (! $this->isCustomRole()) {
            return null;
        }

        return substr((string) $this->role, strlen(RoleCatalog::CUSTOM_PREFIX));
    }

    public function customRoleModel(): ?OrganizationRole
    {
        $slug = $this->customRoleSlug();
        if (! $slug) {
            return null;
        }

        return OrganizationRole::withoutTenant()
            ->where('organization_id', $this->organization_id)
            ->where('slug', $slug)
            ->first();
    }

    public function roleLabel(): string
    {
        if ($enum = $this->roleEnum()) {
            return $enum->label();
        }

        return $this->customRoleModel()?->name
            ?? ucfirst(str_replace(['_', '-'], ' ', (string) $this->customRoleSlug()));
    }

    /**
     * @return list<string>
     */
    public function rolePermissions(): array
    {
        if ($enum = $this->roleEnum()) {
            return $enum->permissions();
        }

        return $this->customRoleModel()?->permissionList() ?? [];
    }

    public function isStudioStaff(): bool
    {
        if ($this->roleEnum() === Role::Client) {
            return false;
        }

        return $this->roleEnum()?->isStaff() ?? $this->isCustomRole();
    }

    public function hasFullAccessRole(): bool
    {
        return (bool) $this->roleEnum()?->hasFullAccess();
    }

    public function canDeleteInOrganization(): bool
    {
        return (bool) $this->roleEnum()?->canDelete();
    }
}
