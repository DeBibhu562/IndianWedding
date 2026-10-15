<?php

namespace Tests\Feature;

use App\Livewire\Platform\WebsiteEditor;
use App\Models\Organization;
use App\Models\Package;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\Money;
use App\Support\SiteCopy;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackagesPageCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_packages_page_header_copy_saves_and_renders_on_public_page(): void
    {
        [$org, $admin] = $this->studioAdmin();

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => 'packages',
            'section' => 'header',
            'key' => 'packages.wedding_heading',
            'label' => 'Wedding packages heading',
            'value' => 'Before',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('page', 'packages')
            ->set('section', 'header')
            ->set('fields.packages.wedding_heading', 'Custom wedding tiers')
            ->call('saveCopy')
            ->assertHasNoErrors();

        SiteCopy::forget($org->id);
        $this->assertSame('Custom wedding tiers', SiteCopy::get('packages.wedding_heading'));

        config(['lumina.public_studio_slug' => $org->slug]);
        $this->get('/packages')->assertSee('Custom wedding tiers');
    }

    public function test_diamond_wedding_package_migration_shape(): void
    {
        $org = Organization::factory()->create(['is_active' => true]);
        Tenant::set($org->id);

        Package::withoutTenant()->create([
            'organization_id' => $org->id,
            'slug' => 'diamond-wedding',
            'name' => 'Diamond',
            'package_type' => 'wedding',
            'price' => Money::fromMajor(249999),
            'is_public' => true,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        config(['lumina.public_studio_slug' => $org->slug]);

        $this->get('/packages')->assertSee('Diamond');
    }

    /**
     * @return array{0: Organization, 1: User}
     */
    private function studioAdmin(): array
    {
        $org = Organization::factory()->create(['is_active' => true, 'slug' => 'cms-packages-test']);
        $admin = User::factory()->create();
        $org->users()->attach($admin->id, ['role' => 'studio_admin', 'accepted_at' => now()]);
        Tenant::set($org->id);

        return [$org, $admin];
    }
}
