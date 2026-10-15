<?php

namespace App\Livewire\Platform;

use App\Models\Faq;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use App\Models\SiteContent;
use App\Models\Testimonial;
use App\Support\SiteContentSections;
use App\Support\SiteCopy;
use App\Support\SocialPlatforms;
use App\Support\Tenant;
use App\Support\WebsiteCmsMenu;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.studio')]
#[Title('Website CMS')]
class WebsiteEditor extends Component
{
    use WithFileUploads;

    public string $page = 'global';

    public string $section = 'brand';

    /** @var array<string, string> */
    public array $fields = [];

    /** @var array<string, TemporaryUploadedFile|null> */
    public array $imageUploads = [];

    public string $testimonial_author = '';

    public string $testimonial_role = '';

    public string $testimonial_quote = '';

    public string $faq_question = '';

    public string $faq_answer = '';

    public string $portfolio_title = '';

    public string $portfolio_image = '';

    public string $portfolio_category = 'wedding';

    /** @var array<int, TemporaryUploadedFile> */
    public array $portfolio_uploads = [];

    public int $heroSlide = 1;

    public function mount(): void
    {
        abort_unless(auth()->user()?->canInOrganization('settings.manage', Tenant::current()), 403);

        $org = $this->organization();
        Tenant::set($org->id);
        SiteContentSections::repairForOrganization($org);
        $this->loadFields();
    }

    public function selectSection(string $page, string $section): void
    {
        $this->page = $page;
        $this->section = $section;
        $this->heroSlide = 1;
        $this->reset('imageUploads');
    }

    public function selectHeroSlide(int $slide): void
    {
        $this->heroSlide = max(1, min(WebsiteCmsMenu::heroSlideCount(), $slide));
    }

    public function loadFields(): void
    {
        $org = $this->organization();
        $flat = SiteContent::withoutTenant()
            ->where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->key => (string) $row->value])
            ->all();

        $this->fields = $this->nestFields($flat);
    }

    public function saveCopy(): void
    {
        $org = $this->organization();

        foreach (Arr::dot($this->imageUploads) as $key => $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs("org/{$org->id}/cms", $filename, 'public');
                data_set($this->fields, $key, Storage::disk('public')->url($path));
            }
        }

        $pageKeys = SiteContent::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('group', $this->contentGroup())
            ->where('section', $this->section)
            ->pluck('key');

        foreach ($pageKeys as $key) {
            if (str_starts_with((string) $key, 'social.')) {
                continue;
            }
            if (! Arr::has($this->fields, $key)) {
                continue;
            }
            SiteContent::withoutTenant()->where('organization_id', $org->id)->where('key', $key)->update([
                'value' => (string) data_get($this->fields, $key),
            ]);
        }

        SiteCopy::forget($org->id);
        $this->reset('imageUploads');
        $this->loadFields();
        session()->flash('status', WebsiteCmsMenu::sectionLabel($this->page, $this->section).' saved.');
    }

    public function saveSocial(): void
    {
        $org = $this->organization();
        $rules = [];
        foreach (SocialPlatforms::definitions() as $def) {
            $rules['fields.'.$def['key']] = ['nullable', 'string', 'max:500'];
        }
        $this->validate($rules);

        foreach (SocialPlatforms::definitions() as $def) {
            $raw = trim($this->fieldValue($def['key']));
            $normalized = $raw === '' ? '' : SocialPlatforms::normalizeUrl($raw, $def['id']);
            if ($raw !== '' && $normalized === '') {
                $this->addError('fields.'.$def['key'], 'Enter a valid URL or phone number for '.$def['label'].'.');
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        foreach (SocialPlatforms::definitions() as $def) {
            $raw = trim($this->fieldValue($def['key']));
            $value = $raw === '' ? '' : SocialPlatforms::normalizeUrl($raw, $def['id']);
            SiteContent::withoutTenant()->where('organization_id', $org->id)->where('key', $def['key'])->update([
                'value' => $value,
            ]);
            data_set($this->fields, $def['key'], $value);
        }

        SiteCopy::forget($org->id);
        $this->loadFields();
        session()->flash('status', 'Social media links saved.');
    }

    public function addTestimonial(): void
    {
        $this->validate([
            'testimonial_author' => ['required', 'string', 'max:120'],
            'testimonial_quote' => ['required', 'string'],
        ]);
        Testimonial::query()->create([
            'organization_id' => $this->organization()->id,
            'author' => $this->testimonial_author,
            'role' => $this->testimonial_role,
            'quote' => $this->testimonial_quote,
            'is_published' => true,
            'rating' => 5,
        ]);
        $this->reset('testimonial_author', 'testimonial_role', 'testimonial_quote');
        session()->flash('status', 'Testimonial added.');
    }

    public function deleteTestimonial(int $id): void
    {
        Testimonial::query()
            ->where('organization_id', $this->organization()->id)
            ->whereKey($id)
            ->delete();
        session()->flash('status', 'Testimonial removed.');
    }

    public function addFaq(): void
    {
        $this->validate(['faq_question' => ['required'], 'faq_answer' => ['required']]);
        Faq::query()->create([
            'organization_id' => $this->organization()->id,
            'question' => $this->faq_question,
            'answer' => $this->faq_answer,
            'is_published' => true,
        ]);
        $this->reset('faq_question', 'faq_answer');
        session()->flash('status', 'FAQ added.');
    }

    public function deleteFaq(int $id): void
    {
        Faq::query()
            ->where('organization_id', $this->organization()->id)
            ->whereKey($id)
            ->delete();
        session()->flash('status', 'FAQ removed.');
    }

    public function updatedPortfolioUploads(): void
    {
        $this->validate($this->portfolioUploadRules(), $this->portfolioUploadMessages());
    }

    public function addPortfolio(): void
    {
        $this->validate(array_merge([
            'portfolio_title' => ['required', 'string', 'max:160'],
            'portfolio_category' => ['nullable', 'string', 'max:80'],
            'portfolio_image' => ['nullable', 'url', 'max:2048'],
        ], $this->portfolioUploadRules()), $this->portfolioUploadMessages());

        $uploads = array_values(array_filter(
            $this->portfolio_uploads,
            fn ($file) => $file instanceof TemporaryUploadedFile,
        ));
        $imageUrl = trim($this->portfolio_image);

        if ($uploads === [] && $imageUrl === '') {
            $this->addError('portfolio_uploads', 'Upload an image (max 2 MB) or paste an image URL.');

            return;
        }

        $org = $this->organization();
        $sort = (int) PortfolioItem::query()->max('sort_order');
        $category = $this->portfolio_category !== '' ? $this->portfolio_category : 'wedding';
        $title = $this->portfolio_title;

        if ($uploads !== []) {
            foreach ($uploads as $index => $file) {
                $sort++;
                $path = $file->storeAs(
                    "org/{$org->id}/portfolio",
                    Str::uuid().'.'.$file->getClientOriginalExtension(),
                    'public',
                );

                $itemTitle = count($uploads) > 1 ? $title.' ('.($index + 1).')' : $title;

                PortfolioItem::query()->create([
                    'organization_id' => $org->id,
                    'title' => $itemTitle,
                    'slug' => Str::slug($itemTitle).'-'.Str::lower(Str::random(4)),
                    'image_path' => $path,
                    'category' => $category,
                    'is_published' => true,
                    'sort_order' => $sort,
                ]);
            }
        } else {
            $sort++;
            PortfolioItem::query()->create([
                'organization_id' => $org->id,
                'title' => $title,
                'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
                'image_path' => $imageUrl,
                'category' => $category,
                'is_published' => true,
                'sort_order' => $sort,
            ]);
        }

        $this->reset('portfolio_title', 'portfolio_image', 'portfolio_uploads');
        session()->flash('status', 'Gallery image added.');
    }

    public function deletePortfolio(int $id): void
    {
        $item = PortfolioItem::query()
            ->where('organization_id', $this->organization()->id)
            ->whereKey($id)
            ->firstOrFail();
        $item->deleteStoredImage();
        $item->delete();
        session()->flash('status', 'Gallery image removed.');
    }

    /**
     * @return array<string, list<string>>
     */
    protected function portfolioUploadRules(): array
    {
        return [
            'portfolio_uploads' => ['nullable', 'array'],
            'portfolio_uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function portfolioUploadMessages(): array
    {
        return [
            'portfolio_uploads.*.max' => 'Each image must be 2 MB or smaller.',
            'portfolio_uploads.*.image' => 'Only image files are allowed.',
            'portfolio_uploads.*.mimes' => 'Use JPG, PNG, WEBP, or GIF images.',
        ];
    }

    public function render()
    {
        $org = $this->organization();
        Tenant::set($org->id);

        $activeModule = WebsiteCmsMenu::sectionModule($this->page, $this->section);

        $activeCopyRows = SiteContent::withoutTenant()
            ->where('organization_id', $org->id)
            ->where('group', $this->contentGroup())
            ->where('section', $this->section)
            ->orderBy('sort_order')
            ->get()
            ->reject(fn ($row) => str_starts_with((string) $row->key, 'social.'));

        $currentPage = collect(WebsiteCmsMenu::pages())->firstWhere('id', $this->page);

        return view('livewire.platform.website-editor', [
            'cmsPages' => WebsiteCmsMenu::pages(),
            'currentPage' => $currentPage,
            'activeCopyRows' => $activeCopyRows,
            'activeModule' => $activeModule,
            'pageLabel' => WebsiteCmsMenu::pageLabel($this->page),
            'sectionLabel' => WebsiteCmsMenu::sectionLabel($this->page, $this->section),
            'testimonials' => Testimonial::query()->latest()->get(),
            'faqs' => Faq::query()->orderBy('sort_order')->get(),
            'portfolio' => PortfolioItem::query()->orderBy('sort_order')->get(),
            'packages' => Package::query()->orderBy('sort_order')->get(),
        ]);
    }

    protected function contentGroup(): string
    {
        return SiteContentSections::contentGroupFor($this->page, $this->section);
    }

    protected function organization(): Organization
    {
        return Tenant::current()
            ?? Organization::query()->where('is_active', true)->firstOrFail();
    }

    protected function fieldValue(string $key): string
    {
        return (string) data_get($this->fields, $key, '');
    }

    /**
     * @param  array<string, string>  $flat
     * @return array<string, mixed>
     */
    protected function nestFields(array $flat): array
    {
        $nested = [];

        foreach ($flat as $key => $value) {
            data_set($nested, $key, $value);
        }

        return $nested;
    }
}
