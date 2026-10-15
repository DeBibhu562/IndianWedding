<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'whatsapp', 'avatar_path', 'is_super_admin', 'is_active', 'timezone', 'locale', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_users')
            ->withPivot(['role', 'is_owner', 'permissions'])
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }

    public function membershipIn(?Organization $organization = null): ?OrganizationUser
    {
        $organization ??= \App\Support\Tenant::current();

        if (! $organization) {
            return $this->memberships()->first();
        }

        return $this->memberships()
            ->where('organization_id', $organization->id)
            ->first();
    }

    public function roleIn(?Organization $organization = null): ?Role
    {
        $membership = $this->membershipIn($organization);

        if (! $membership) {
            return null;
        }

        return $membership->roleEnum();
    }

    public function isOrganizationOwner(?Organization $organization = null): bool
    {
        return (bool) $this->membershipIn($organization)?->is_owner;
    }

    public function hasFullStudioAccess(?Organization $organization = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $role = $this->roleIn($organization);

        return $role?->hasFullAccess()
            || $this->isOrganizationOwner($organization);
    }

    /**
     * Permanent delete is reserved for Studio Admin (and platform super admin).
     */
    public function canDeleteInOrganization(?Organization $organization = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->roleIn($organization)?->canDelete() ?? false;
    }

    public function canInOrganization(string $permission, ?Organization $organization = null): bool
    {
        $organization ??= \App\Support\Tenant::current();

        if ($this->is_super_admin) {
            return true;
        }

        $role = $this->roleIn($organization);

        if ($role?->hasFullAccess() || $this->isOrganizationOwner($organization)) {
            return true;
        }

        $membership = $this->membershipIn($organization);

        if (! $membership) {
            return false;
        }

        $custom = is_array($membership->permissions) ? $membership->permissions : [];
        $grants = $custom['grants'] ?? [];
        $revokes = $custom['revokes'] ?? [];

        if (in_array($permission, $revokes, true)) {
            return false;
        }

        if (in_array($permission, $grants, true)) {
            return true;
        }

        return in_array($permission, $membership->rolePermissions(), true);
    }

    public function isClient(?Organization $organization = null): bool
    {
        return $this->roleIn($organization) === Role::Client;
    }
}
