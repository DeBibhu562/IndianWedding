<?php

namespace Tests\Feature;

use App\Livewire\Studio\Team\Create;
use App\Models\Organization;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TeamMemberCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_team_member_with_password_and_phone(): void
    {
        [$org, $admin] = $this->studioAdmin();

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(Create::class)
            ->set('name', 'Aisha Khan')
            ->set('email', 'aisha@unikstudio.in')
            ->set('phone', '+91 9818361412')
            ->set('password', 'secure-pass-10')
            ->set('password_confirmation', 'secure-pass-10')
            ->set('role', 'photographer')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.team.index'));

        $user = User::query()->where('email', 'aisha@unikstudio.in')->first();

        $this->assertNotNull($user);
        $this->assertSame('Aisha Khan', $user->name);
        $this->assertSame('+91 9818361412', $user->phone);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('secure-pass-10', $user->password));

        $this->assertDatabaseHas('organization_users', [
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'photographer',
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
