<?php

namespace App\Support;

class WebsiteCmsMenu
{
    /**
     * Full website CMS tree: each public page with its own sub-menu sections.
     *
     * @return list<array{
     *     id: string,
     *     label: string,
     *     url?: string,
     *     sections: list<array{id: string, label: string, module?: string}>
     * }>
     */
    public static function pages(): array
    {
        return [
            [
                'id' => 'global',
                'label' => 'Global settings',
                'url' => '/',
                'sections' => [
                    ['id' => 'brand', 'label' => 'Brand & identity'],
                    ['id' => 'nav', 'label' => 'Menu labels'],
                    ['id' => 'footer', 'label' => 'Footer'],
                    ['id' => 'seo', 'label' => 'SEO defaults'],
                    ['id' => 'social', 'label' => 'Social media links', 'module' => 'social'],
                ],
            ],
            [
                'id' => 'home',
                'label' => 'Home page',
                'url' => '/',
                'sections' => [
                    ['id' => 'hero', 'label' => 'Hero slider (5 tabs)'],
                    ['id' => 'story', 'label' => 'Story section'],
                    ['id' => 'services', 'label' => 'Services preview'],
                    ['id' => 'moments', 'label' => 'Stats & counters'],
                    ['id' => 'guide', 'label' => 'Wedding guide'],
                    ['id' => 'portfolio', 'label' => 'Portfolio strip'],
                    ['id' => 'rsvp', 'label' => 'RSVP section'],
                    ['id' => 'feedbacks', 'label' => 'Testimonials preview'],
                    ['id' => 'instagram', 'label' => 'Instagram captions'],
                ],
            ],
            [
                'id' => 'about',
                'label' => 'About page',
                'url' => '/about',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content & images'],
                ],
            ],
            [
                'id' => 'services',
                'label' => 'Services page',
                'url' => '/services',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                ],
            ],
            [
                'id' => 'production',
                'label' => 'Production page',
                'url' => '/production',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                ],
            ],
            [
                'id' => 'packages',
                'label' => 'Pricing page',
                'url' => '/packages',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                ],
            ],
            [
                'id' => 'gallery',
                'label' => 'Gallery page',
                'url' => '/gallery',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                    ['id' => 'portfolio', 'label' => 'Gallery images', 'module' => 'portfolio'],
                ],
            ],
            [
                'id' => 'contact',
                'label' => 'Contact page',
                'url' => '/contact',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                ],
            ],
            [
                'id' => 'reservation',
                'label' => 'Booking page',
                'url' => '/book-consultation',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                ],
            ],
            [
                'id' => 'testimonials',
                'label' => 'Testimonials page',
                'url' => '/testimonials',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                    ['id' => 'testimonials', 'label' => 'Testimonial entries', 'module' => 'testimonials'],
                ],
            ],
            [
                'id' => 'faq',
                'label' => 'FAQ page',
                'url' => '/faq',
                'sections' => [
                    ['id' => 'content', 'label' => 'Page content'],
                    ['id' => 'faq', 'label' => 'FAQ entries', 'module' => 'faq'],
                ],
            ],
            [
                'id' => 'lead_popup',
                'label' => 'Enquiry popup',
                'url' => '/',
                'sections' => [
                    ['id' => 'popup', 'label' => 'Popup content'],
                ],
            ],
        ];
    }

    /** @deprecated Use pages() */
    public static function tree(): array
    {
        return self::pages();
    }

    public static function pageLabel(string $pageId): string
    {
        foreach (self::pages() as $page) {
            if ($page['id'] === $pageId) {
                return $page['label'];
            }
        }

        return ucfirst(str_replace('_', ' ', $pageId));
    }

    public static function sectionLabel(string $pageId, string $sectionId): string
    {
        foreach (self::pages() as $page) {
            if ($page['id'] !== $pageId) {
                continue;
            }
            foreach ($page['sections'] as $section) {
                if ($section['id'] === $sectionId) {
                    return $section['label'];
                }
            }
        }

        return ucfirst(str_replace('_', ' ', $sectionId));
    }

    public static function sectionModule(string $pageId, string $sectionId): ?string
    {
        foreach (self::pages() as $page) {
            if ($page['id'] !== $pageId) {
                continue;
            }
            foreach ($page['sections'] as $section) {
                if ($section['id'] === $sectionId) {
                    return $section['module'] ?? null;
                }
            }
        }

        return null;
    }

    public static function copyGroupLabel(string $group): string
    {
        return self::pageLabel($group);
    }

    public static function heroSlideCount(): int
    {
        return 5;
    }

    public static function isHeroSlideKey(string $key, int $slide): bool
    {
        return preg_match('/^home\.slide_'.$slide.'_/', $key) === 1;
    }

    public static function isAnyHeroSlideKey(string $key): bool
    {
        return preg_match('/^home\.slide_\d+_/', $key) === 1;
    }
}
