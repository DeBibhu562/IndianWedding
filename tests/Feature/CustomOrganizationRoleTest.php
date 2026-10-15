<?php

namespace Tests\Feature;

use App\Livewire\Studio\Team\Create;
use App\Livewire\Studio\Team\RoleCreate;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomOrganizationRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_admin_can_create_custom_role_and_assign_to_member(): void
    {
        [$org, $admin] = $this->studioAdmin();

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(RoleCreate::class)
            ->set('name', 'Drone Operator')
            ->set('description', 'Aerial coverage')
            ->set('permissionStates.files.upload', true)
            ->set('permissionStates.calendar.view', true)
            ->call('save')
            ->assertRedirect(route('app.team.roles'));

        $custom = OrganizationRole::withoutTenant()->where('organization_id', $org->id)->first();
        $this->assertNotNull($custom);
        $this->assertSame('drone-operator', $custom->slug);

        Livewire::test(Create::class)
            ->set('name', 'Vikram Drone')
            ->set('email', 'drone@unikstudio.in')
            ->set('password', 'secure-pass-10')
            ->set('password_confirmation', 'secure-pass-10')
            ->set('role', $custom->storageKey())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('organization_users', [
            'organization_id' => $org->id,
            'role' => 'custom:drone-operator',
        ]);
    }

    /**
     * @return array{0: Organization, 1: User}
     */
    private function studioAdmin(): array
    {
        $org = Organization::factory()->create(['is_active' => true]);
        $admin = User::factory()->create();
        $org->users()->attach($admin->id, ['role' => 'studio_admin', 'accepted_at' => now()]);
        Tenant::set($org->id);

        return [$org, $admin];
    }
}
