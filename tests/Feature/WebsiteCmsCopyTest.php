<?php

namespace Tests\Feature;

use App\Livewire\Platform\WebsiteEditor;
use App\Models\Organization;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\SiteCopy;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteCmsCopyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_save_persists_dotted_content_keys_and_clears_site_copy_cache(): void
    {
        [$org, $admin] = $this->studioAdmin();

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => 'brand',
            'section' => 'brand',
            'key' => 'brand.tagline',
            'label' => 'Tagline',
            'value' => 'Original tagline',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        SiteCopy::forget($org->id);
        $this->assertSame('Original tagline', SiteCopy::get('brand.tagline'));

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('page', 'global')
            ->set('section', 'brand')
            ->set('fields.brand.tagline', 'Updated from CMS')
            ->call('saveCopy')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_contents', [
            'organization_id' => $org->id,
            'key' => 'brand.tagline',
            'value' => 'Updated from CMS',
        ]);

        $this->assertSame('Updated from CMS', SiteCopy::get('brand.tagline'));
    }

    public function test_single_dot_keys_save_from_cms(): void
    {
        [$org, $admin] = $this->studioAdmin();

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => 'brand',
            'section' => 'brand',
            'key' => 'brand.probe_brand',
            'label' => 'Probe',
            'value' => 'Before',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('page', 'global')
            ->set('section', 'brand')
            ->set('fields.brand.probe_brand', 'After')
            ->call('saveCopy')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_contents', [
            'organization_id' => $org->id,
            'key' => 'brand.probe_brand',
            'value' => 'After',
        ]);
    }

    public function test_hero_slider_step_saves_slide_tab_label(): void
    {
        [$org, $admin] = $this->studioAdmin();

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => 'home',
            'section' => 'hero',
            'key' => 'home.slide_3_tab',
            'label' => 'Slide 3 tab label',
            'value' => 'Candid Documentation',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('page', 'home')
            ->set('section', 'hero')
            ->call('selectHeroSlide', 3)
            ->set('fields.home.slide_3_tab', 'My custom tab')
            ->call('saveCopy')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_contents', [
            'organization_id' => $org->id,
            'key' => 'home.slide_3_tab',
            'value' => 'My custom tab',
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
