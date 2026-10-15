<?php

namespace App\Livewire\Studio\Packages;

use App\Livewire\Studio\Packages\Concerns\ManagesPackageFeatures;
use App\Livewire\Studio\Packages\Concerns\ManagesPackageIncludes;
use App\Models\Package;
use App\Support\Money;
use App\Support\PackageOptions;
use Illuminate\Validation\Rule;
use App\Support\Tenant;
use App\Support\YoutubeEmbed;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New service')]
class Create extends Component
{
    use ManagesPackageFeatures, ManagesPackageIncludes;

    public string $package_type = 'service';

    public string $name = '';

    public int $price = 0;

    public int $duration_hours = 8;

    public int $photographer_count = 1;

    public int $videographer_count = 0;

    public int $edited_photos = 200;

    public string $description = '';

    public string $youtube_url = '';

    public function mount(): void
    {
        $type = request()->query('type');
        if (is_string($type) && in_array($type, PackageOptions::typeKeys(), true)) {
            $this->package_type = $type;
        }
    }

    public function save()
    {
        $this->authorize('create', Package::class);

        $data = $this->validate([
            'package_type' => ['required', Rule::in(PackageOptions::typeKeys())],
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'photographer_count' => ['required', 'integer', 'min:0'],
            'videographer_count' => ['required', 'integer', 'min:0'],
            'edited_photos' => ['required', 'integer', 'min:0'],
            ...$this->includeValidationRules(),
            'description' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! YoutubeEmbed::isValid($value)) {
                    $fail('Enter a valid YouTube link (watch, youtu.be, or shorts URL).');
                }
            }],
        ]);

        $package = Package::query()->create([
            ...$data,
            'youtube_url' => trim($this->youtube_url) ?: null,
            'price' => Money::fromMajor($this->price),
            'organization_id' => Tenant::requireId(),
            'slug' => Str::slug($this->name).'-'.Str::lower(Str::random(4)),
            'is_active' => true,
            'is_public' => true,
            'sort_order' => (int) Package::query()->max('sort_order') + 1,
        ]);

        $this->syncFeatureItems($package);

        session()->flash('status', 'Package created.');

        return $this->redirect(route('app.packages.index'), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', Package::class);

        return view('livewire.studio.packages.create');
    }
}
