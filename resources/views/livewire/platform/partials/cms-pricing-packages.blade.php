@php
    use App\Support\Money;
    use App\Support\PackageOptions;

    $type = match ($activeModule) {
        'pricing_wedding' => 'wedding',
        'pricing_addons' => 'addon',
        default => null,
    };
    $listed = $type
        ? $packages->where('package_type', $type)->sortBy('sort_order')->values()
        : collect();
    $typeLabel = $type ? PackageOptions::typeLabel($type) : 'Packages';
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
        <p class="text-sm text-[#16120f]/65">
            These entries appear on the live <a href="/packages" target="_blank" rel="noopener" class="text-[#9b7b4b] underline">pricing page</a>.
            Edit prices, features, and photos in the full editor.
        </p>
        @if ($type)
            <a href="{{ route('app.packages.create', ['type' => $type]) }}" class="shrink-0 rounded-full bg-[#16120f] px-4 py-2 text-sm font-medium text-white">
                Add {{ strtolower($typeLabel) }}
            </a>
        @endif
    </div>

    @if ($listed->isEmpty())
        <p class="rounded-[28px] bg-white p-8 text-sm text-[#16120f]/60 shadow-sm ring-1 ring-black/5">
            No {{ strtolower($typeLabel) }} yet. Use the button above or
            <a href="{{ route('app.packages.index') }}" class="text-[#9b7b4b] underline">Packages & services</a> in the studio.
        </p>
    @else
        <div class="space-y-3">
            @foreach ($listed as $package)
                <article wire:key="cms-package-{{ $package->id }}" class="flex flex-wrap items-center gap-4 rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $package->name }}</h3>
                        <p class="mt-1 text-sm text-[#16120f]/55">
                            @if ($package->package_type === 'wedding')
                                {{ Money::format($package->price) }}
                            @else
                                {{ $package->typeLabel() }}
                            @endif
                            · {{ $package->is_public ? 'Visible on site' : 'Hidden from site' }}
                            · {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </p>
                        @if ($package->description)
                            <p class="mt-2 line-clamp-2 text-sm text-[#16120f]/70">{{ $package->description }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="movePricingPackage({{ $package->id }}, -1)" class="rounded-full bg-[#f6f1ea] px-3 py-1.5 text-xs" title="Move up">↑</button>
                        <button type="button" wire:click="movePricingPackage({{ $package->id }}, 1)" class="rounded-full bg-[#f6f1ea] px-3 py-1.5 text-xs" title="Move down">↓</button>
                        <button type="button" wire:click="togglePricingPackagePublic({{ $package->id }})" class="rounded-full bg-[#f6f1ea] px-3 py-1.5 text-xs">
                            {{ $package->is_public ? 'Hide' : 'Show' }}
                        </button>
                        <a href="{{ route('app.packages.edit', $package) }}" class="rounded-full bg-[#16120f] px-4 py-2 text-xs font-medium text-white">Edit</a>
                        <a href="{{ $package->publicUrl() }}" target="_blank" rel="noopener" class="rounded-full bg-white px-3 py-2 text-xs ring-1 ring-black/10">View</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
