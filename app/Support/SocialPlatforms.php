<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\SiteContent;

class SocialPlatforms
{
    /**
     * @return list<array{key: string, id: string, label: string, placeholder: string, sort: int}>
     */
    public static function definitions(): array
    {
        return [
            ['id' => 'instagram', 'key' => 'social.instagram', 'label' => 'Instagram', 'placeholder' => 'https://instagram.com/yourstudio', 'sort' => 10],
            ['id' => 'facebook', 'key' => 'social.facebook', 'label' => 'Facebook', 'placeholder' => 'https://facebook.com/yourstudio', 'sort' => 20],
            ['id' => 'youtube', 'key' => 'social.youtube', 'label' => 'YouTube', 'placeholder' => 'https://youtube.com/@yourstudio', 'sort' => 30],
            ['id' => 'whatsapp', 'key' => 'social.whatsapp', 'label' => 'WhatsApp', 'placeholder' => 'https://wa.me/919818361412', 'sort' => 40],
            ['id' => 'x', 'key' => 'social.x', 'label' => 'X (Twitter)', 'placeholder' => 'https://x.com/yourstudio', 'sort' => 50],
            ['id' => 'linkedin', 'key' => 'social.linkedin', 'label' => 'LinkedIn', 'placeholder' => 'https://linkedin.com/company/yourstudio', 'sort' => 60],
            ['id' => 'tiktok', 'key' => 'social.tiktok', 'label' => 'TikTok', 'placeholder' => 'https://tiktok.com/@yourstudio', 'sort' => 70],
            ['id' => 'pinterest', 'key' => 'social.pinterest', 'label' => 'Pinterest', 'placeholder' => 'https://pinterest.com/yourstudio', 'sort' => 80],
            ['id' => 'threads', 'key' => 'social.threads', 'label' => 'Threads', 'placeholder' => 'https://threads.net/@yourstudio', 'sort' => 90],
            ['id' => 'snapchat', 'key' => 'social.snapchat', 'label' => 'Snapchat', 'placeholder' => 'https://snapchat.com/add/yourstudio', 'sort' => 100],
            ['id' => 'vimeo', 'key' => 'social.vimeo', 'label' => 'Vimeo', 'placeholder' => 'https://vimeo.com/yourstudio', 'sort' => 110],
            ['id' => 'telegram', 'key' => 'social.telegram', 'label' => 'Telegram', 'placeholder' => 'https://t.me/yourstudio', 'sort' => 120],
            ['id' => 'behance', 'key' => 'social.behance', 'label' => 'Behance', 'placeholder' => 'https://behance.net/yourstudio', 'sort' => 130],
        ];
    }

    public static function syncForOrganization(Organization $organization): void
    {
        foreach (self::definitions() as $def) {
            SiteContent::withoutTenant()->updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'key' => $def['key'],
                ],
                [
                    'group' => 'social',
                    'section' => 'social',
                    'label' => $def['label'],
                    'value' => '',
                    'type' => 'input',
                    'sort_order' => $def['sort'],
                ]
            );
        }
    }

    /**
     * @return list<array{id: string, label: string, url: string}>
     */
    public static function configuredLinks(?int $organizationId = null): array
    {
        $links = [];
        foreach (self::definitions() as $def) {
            $raw = trim(site($def['key'], ''));
            $url = self::normalizeUrl($raw, $def['id']);
            if ($url === '') {
                continue;
            }
            $links[] = [
                'id' => $def['id'],
                'label' => $def['label'],
                'url' => $url,
            ];
        }

        return $links;
    }

    public static function normalizeUrl(string $value, string $platformId): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if ($platformId === 'whatsapp') {
            if (preg_match('/^\+?[\d\s\-()]+$/', $value)) {
                $digits = preg_replace('/\D+/', '', $value);

                return $digits !== '' ? 'https://wa.me/'.$digits : '';
            }
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
        }

        if (str_starts_with($value, '//')) {
            $candidate = 'https:'.$value;

            return filter_var($candidate, FILTER_VALIDATE_URL) ? $candidate : '';
        }

        if (str_contains($value, '.') && ! str_contains($value, ' ')) {
            $candidate = 'https://'.$value;

            return filter_var($candidate, FILTER_VALIDATE_URL) ? $candidate : '';
        }

        return '';
    }
}
