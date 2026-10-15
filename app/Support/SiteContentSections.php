<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\SiteContent;

class SiteContentSections
{
    public static function contentGroupFor(string $pageId, string $sectionId): string
    {
        return $pageId === 'global' ? $sectionId : $pageId;
    }

    /**
     * @return list<array{page: string, section: string, group: string, module: ?string}>
     */
    public static function copySections(): array
    {
        $sections = [];

        foreach (WebsiteCmsMenu::pages() as $page) {
            foreach ($page['sections'] as $section) {
                if (isset($section['module'])) {
                    continue;
                }

                $sections[] = [
                    'page' => $page['id'],
                    'section' => $section['id'],
                    'group' => self::contentGroupFor($page['id'], $section['id']),
                ];
            }
        }

        return $sections;
    }

    public static function repairForOrganization(Organization $organization): void
    {
        SocialPlatforms::syncForOrganization($organization);

        SiteContent::withoutTenant()
            ->where('organization_id', $organization->id)
            ->whereNull('section')
            ->orderBy('id')
            ->each(function (SiteContent $row): void {
                $row->update([
                    'section' => self::resolveSection((string) $row->group, (string) $row->key),
                ]);
            });

        SiteContent::withoutTenant()
            ->where('organization_id', $organization->id)
            ->where('key', 'like', 'social.%')
            ->where(function ($query) {
                $query->whereNull('section')->orWhere('section', '!=', 'social');
            })
            ->update(['group' => 'social', 'section' => 'social']);
    }

    public static function resolveSection(string $group, string $key): string
    {
        return match (true) {
            str_starts_with($key, 'brand.') => 'brand',
            str_starts_with($key, 'nav.') => 'nav',
            str_starts_with($key, 'footer.') => 'footer',
            str_starts_with($key, 'seo.') => 'seo',
            str_starts_with($key, 'social.') => 'social',
            str_starts_with($key, 'home.slide_') => 'hero',
            in_array($key, ['home.hero_image', 'home.kicker', 'home.headline', 'home.cta'], true) => 'hero',
            str_starts_with($key, 'home.story_') => 'story',
            str_starts_with($key, 'home.services_') => 'services',
            str_starts_with($key, 'home.moments_') || str_starts_with($key, 'home.stat_') => 'moments',
            str_starts_with($key, 'home.guide_') => 'guide',
            str_starts_with($key, 'home.portfolio_') => 'portfolio',
            str_starts_with($key, 'home.rsvp_') => 'rsvp',
            str_starts_with($key, 'home.feedbacks_') => 'feedbacks',
            str_starts_with($key, 'home.instagram_') => 'instagram',
            str_starts_with($key, 'lead_popup.') => 'popup',
            str_starts_with($key, 'about.') => 'content',
            str_starts_with($key, 'services.') => 'content',
            str_starts_with($key, 'gallery.') => 'content',
            str_starts_with($key, 'contact.') => 'content',
            str_starts_with($key, 'reservation.') => 'content',
            str_starts_with($key, 'testimonials.') => 'content',
            str_starts_with($key, 'faq.') => 'content',
            str_starts_with($key, 'team.') => 'content',
            str_starts_with($key, 'packages.') => 'content',
            str_starts_with($key, 'production.') => 'content',
            default => $group === 'global' ? 'content' : ($group === $key ? 'content' : 'content'),
        };
    }
}
