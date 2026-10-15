@php
    use App\Support\WebsiteCmsMenu;

    $slideCount = WebsiteCmsMenu::heroSlideCount();
    $heroGeneralRows = $activeCopyRows->reject(fn ($row) => WebsiteCmsMenu::isAnyHeroSlideKey($row->key));
    $heroSlideRows = $activeCopyRows->filter(fn ($row) => WebsiteCmsMenu::isHeroSlideKey($row->key, $heroSlide));
@endphp

<form wire:submit="saveCopy" class="space-y-6">
    @if ($heroGeneralRows->isNotEmpty())
        <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
            <h3 class="text-sm font-semibold text-[#16120f]">Shared hero settings</h3>
            <p class="mt-1 text-xs text-[#16120f]/55">Headline and button text used in other places on the site (login page, CTAs).</p>
            <div class="mt-5 grid gap-5">
                @foreach ($heroGeneralRows as $row)
                    <x-cms-field :row="$row" :value="data_get($fields, $row->key, '')" wire:key="cms-field-{{ $row->key }}" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="rounded-[28px] bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-[#16120f]">Home slider — 5 tabs</h3>
                <p class="mt-1 text-xs text-[#16120f]/55">Each tab matches one slide on the homepage hero (bottom bar: 01–05).</p>
            </div>
            <a href="/" target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-[#9b7b4b] hover:underline">Preview homepage →</a>
        </div>

        <nav class="mt-5 grid grid-cols-5 gap-1 rounded-2xl bg-[#f6f1ea] p-1" aria-label="Hero slide steps">
            @for ($n = 1; $n <= $slideCount; $n++)
                @php
                    $tabPreview = trim((string) data_get($fields, "home.slide_{$n}_tab", ''));
                    $tabPreview = $tabPreview !== '' ? $tabPreview : "Slide {$n}";
                @endphp
                <button
                    type="button"
                    wire:click="selectHeroSlide({{ $n }})"
                    class="min-w-0 rounded-xl px-2 py-2.5 text-center transition sm:px-3 sm:py-3 {{ $heroSlide === $n ? 'bg-[#16120f] text-white shadow-sm' : 'text-[#16120f]/70 hover:bg-white hover:text-[#16120f]' }}"
                    aria-current="{{ $heroSlide === $n ? 'step' : 'false' }}"
                >
                    <span class="block text-[10px] font-semibold tracking-[0.14em] {{ $heroSlide === $n ? 'text-[#e2c48a]' : 'text-[#c4a574]' }}">{{ str_pad((string) $n, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="mt-1 block truncate text-[10px] leading-tight sm:text-xs">{{ $tabPreview }}</span>
                </button>
            @endfor
        </nav>

        <div class="mt-6 border-t border-black/5 pt-6">
            <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#9b7b4b]">
                Editing slide {{ str_pad((string) $heroSlide, 2, '0', STR_PAD_LEFT) }}
            </p>
            @if ($heroSlideRows->isEmpty())
                <p class="text-sm text-[#16120f]/60">No fields for this slide. Run <code class="rounded bg-[#f6f1ea] px-1">php artisan migrate</code> to seed slider content.</p>
            @else
                <div class="grid gap-5">
                    @foreach ($heroSlideRows as $row)
                        <x-cms-field :row="$row" :value="data_get($fields, $row->key, '')" wire:key="cms-hero-slide-{{ $heroSlide }}-{{ $row->key }}" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white shadow-sm">
        Save hero slider
    </button>
</form>
