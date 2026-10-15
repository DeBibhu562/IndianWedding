<?php

namespace Tests\Feature;

use App\Livewire\Platform\WebsiteEditor;
use App\Models\Organization;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\SiteContentSections;
use App\Support\SiteCopy;
use App\Support\Tenant;
use App\Support\WebsiteCmsMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteCmsAllMenusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array{page: string, section: string, group: string}  $meta
     */
    private function seedProbeField(array $meta, Organization $org): string
    {
        $key = "{$meta['group']}.probe_{$meta['section']}";

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => $meta['group'],
            'section' => $meta['section'],
            'key' => $key,
            'label' => 'Test field',
            'value' => 'Before',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        return $key;
    }

    public function test_representative_cms_sections_save_and_refresh_site_copy(): void
    {
        [$org, $admin] = $this->studioAdmin();
        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        $samples = [
            ['page' => 'global', 'section' => 'brand', 'group' => 'brand'],
            ['page' => 'home', 'section' => 'story', 'group' => 'home'],
            ['page' => 'production', 'section' => 'content', 'group' => 'production'],
            ['page' => 'lead_popup', 'section' => 'popup', 'group' => 'lead_popup'],
        ];

        foreach ($samples as $meta) {
            $key = $this->seedProbeField($meta, $org);
            $expected = "Updated {$meta['page']}/{$meta['section']}";

            Livewire::test(WebsiteEditor::class)
                ->set('page', $meta['page'])
                ->set('section', $meta['section'])
                ->set('fields.'.$key, $expected)
                ->call('saveCopy')
                ->assertHasNoErrors();

            SiteCopy::forget($org->id);

            $this->assertDatabaseHas('site_contents', [
                'organization_id' => $org->id,
                'key' => $key,
                'value' => $expected,
            ]);
            $this->assertSame($expected, SiteCopy::get($key));
        }
    }

    public function test_social_rows_are_repaired_for_organization(): void
    {
        [$org, $admin] = $this->studioAdmin();

        SiteContent::withoutTenant()->create([
            'organization_id' => $org->id,
            'group' => 'social',
            'section' => null,
            'key' => 'social.instagram',
            'label' => 'Instagram',
            'value' => 'https://instagram.com/unik_studioo',
            'type' => 'input',
            'sort_order' => 1,
        ]);

        SiteContentSections::repairForOrganization($org);

        $this->assertDatabaseHas('site_contents', [
            'organization_id' => $org->id,
            'key' => 'social.instagram',
            'section' => 'social',
        ]);
    }

    public function test_cms_menu_copy_sections_match_website_cms_menu(): void
    {
        $menuCopySectionCount = 0;

        foreach (WebsiteCmsMenu::pages() as $page) {
            foreach ($page['sections'] as $section) {
                if (! isset($section['module'])) {
                    $menuCopySectionCount++;
                }
            }
        }

        $this->assertSame($menuCopySectionCount, count(SiteContentSections::copySections()));
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
