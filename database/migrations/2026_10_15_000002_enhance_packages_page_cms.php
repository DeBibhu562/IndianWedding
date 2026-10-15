<?php

use App\Models\Organization;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\SiteContent;
use App\Support\Money;
use App\Support\SiteCopy;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Organization::query()->where('is_active', true)->get() as $org) {
            $this->reassignPackageCopySections($org->id);
            $this->seedPackagesPageCopy($org->id);
            $this->seedDiamondWeddingPackage($org->id);
            SiteCopy::forget($org->id);
        }
    }

    protected function reassignPackageCopySections(int $orgId): void
    {
        SiteContent::withoutTenant()
            ->where('organization_id', $orgId)
            ->where('group', 'packages')
            ->each(function (SiteContent $row): void {
                $section = $this->sectionForKey((string) $row->key);
                if ($section !== $row->section) {
                    $row->update(['section' => $section]);
                }
            });
    }

    protected function sectionForKey(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'packages.services_') => 'services_preview',
            str_starts_with($key, 'packages.production_') => 'production_preview',
            str_starts_with($key, 'packages.addons_') => 'addons',
            default => 'header',
        };
    }

    protected function seedPackagesPageCopy(int $orgId): void
    {
        $order = (int) SiteContent::withoutTenant()->where('organization_id', $orgId)->max('sort_order');

        $rows = [
            ['header', 'packages.eyebrow', 'Top label (eyebrow)', 'Pricing', 'input'],
            ['header', 'packages.wedding_heading', 'Wedding packages heading', 'Wedding packages', 'input'],
            ['header', 'packages.wedding_intro', 'Wedding packages intro', 'Fixed pricing tiers for photography, videography and albums — tap a card for the full breakdown.', 'textarea'],
            ['header', 'packages.link_services', 'Quick link — services', 'All services', 'input'],
            ['header', 'packages.link_production', 'Quick link — production', 'Production & films', 'input'],
            ['header', 'packages.empty_wedding', 'Empty wedding list message', 'Wedding packages will appear here once added in the studio.', 'input'],
            ['services_preview', 'packages.services_heading', 'Services block heading', 'Our Services', 'input'],
            ['services_preview', 'packages.services_intro', 'Services block intro', 'A professional service portfolio covering wedding packages and end-to-end production services for music videos, films, web series, serials, corporate shoots and product campaigns.', 'textarea'],
            ['services_preview', 'packages.services_link', 'Services block link label', 'View all services →', 'input'],
            ['production_preview', 'packages.production_heading', 'Production block heading', 'Production & Creative Services', 'input'],
            ['production_preview', 'packages.production_intro', 'Production block intro', 'From concept to screen, we provide end-to-end production solutions for music videos, short films, web series, television/serial shoots, corporate films, product shoots and digital content.', 'textarea'],
            ['production_preview', 'packages.production_link', 'Production block link label', 'View all production →', 'input'],
            ['production_preview', 'packages.production_featured_heading', 'Featured projects label', 'Featured projects', 'input'],
            ['production_preview', 'packages.production_services_heading', 'Production services label', 'Production services', 'input'],
        ];

        foreach ($rows as [$section, $key, $label, $value, $type]) {
            SiteContent::withoutTenant()->updateOrCreate(
                ['organization_id' => $orgId, 'key' => $key],
                [
                    'group' => 'packages',
                    'section' => $section,
                    'label' => $label,
                    'value' => $value,
                    'type' => $type,
                    'sort_order' => ++$order,
                ]
            );
        }
    }

    protected function seedDiamondWeddingPackage(int $orgId): void
    {
        $items = [
            'Candid Photography — 2 Photographers',
            'Traditional Photography — 1 Photographer',
            'Cinematic Videography — 2 Videographers',
            'Drone Photography & Cinematography',
            'Cinematic Wedding Film — 10–14 Minutes',
            'Full Wedding Ceremony Video',
            '8 Premium Instagram/Reels Videos',
            'Pre-Wedding Couple Shoot',
            'Pre-Wedding Cinematic Video',
            '900+ Professionally Edited Photos',
            'Premium Photobook — 45 Pages',
            'Wedding Teaser — 90–120 Seconds',
            'High-Resolution Digital Delivery',
            'Extended Full-Day Coverage',
            'Dedicated Creative Team',
        ];

        $existing = Package::withoutTenant()
            ->where('organization_id', $orgId)
            ->where('slug', 'diamond-wedding')
            ->first();

        $sort = (int) Package::withoutTenant()
            ->where('organization_id', $orgId)
            ->where('package_type', 'wedding')
            ->max('sort_order');

        $package = Package::withoutTenant()->updateOrCreate(
            ['organization_id' => $orgId, 'slug' => 'diamond-wedding'],
            [
                'name' => 'Diamond',
                'description' => 'Enhanced cinematic coverage for medium and large celebrations',
                'package_type' => 'wedding',
                'price' => Money::fromMajor(249999),
                'duration_hours' => 14,
                'photographer_count' => 3,
                'videographer_count' => 2,
                'edited_photos' => 900,
                'includes_album' => true,
                'includes_video' => true,
                'includes_pre_wedding' => true,
                'includes_drone' => true,
                'is_public' => true,
                'is_active' => true,
                'sort_order' => $existing?->sort_order ?? ($sort + 1),
            ]
        );

        if ($package->items()->count() === 0) {
            foreach ($items as $i => $name) {
                PackageItem::withoutTenant()->create([
                    'organization_id' => $orgId,
                    'package_id' => $package->id,
                    'name' => $name,
                    'sort_order' => $i,
                ]);
            }
        }

        $destination = Package::withoutTenant()
            ->where('organization_id', $orgId)
            ->where('slug', 'destination-wedding')
            ->first();

        if ($destination && $destination->sort_order <= $package->sort_order) {
            $destination->update(['sort_order' => $package->sort_order + 1]);
        }
    }
};
