<div x-data="{ openPage: @js($page) }">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-3xl text-[#16120f] md:text-4xl">Website CMS</h1>
            <p class="mt-1 text-sm text-[#16120f]/60">Edit every page, image, and section of your public website.</p>
        </div>
        @if ($currentPage['url'] ?? null)
            <a href="{{ $currentPage['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium shadow-sm ring-1 ring-black/5 transition hover:bg-[#f6f1ea]">
                Preview {{ $pageLabel }}
                <x-studio.icon name="website" class="h-4 w-4" />
            </a>
        @endif
    </div>

    <x-swal-flash />

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        {{-- Page & sub-menu navigation --}}
        <aside class="mb-8 lg:col-span-3 lg:mb-0">
            <nav class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto studio-scroll space-y-2 rounded-[28px] bg-white p-3 shadow-sm ring-1 ring-black/5" aria-label="Website CMS pages">
                @foreach ($cmsPages as $cmsPage)
                    <div class="rounded-2xl {{ $page === $cmsPage['id'] ? 'bg-[#f6f1ea]' : '' }}">
                        <button
                            type="button"
                            @click="openPage = openPage === @js($cmsPage['id']) ? null : @js($cmsPage['id'])"
                            wire:click="selectSection('{{ $cmsPage['id'] }}', '{{ $cmsPage['sections'][0]['id'] }}')"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-medium transition {{ $page === $cmsPage['id'] ? 'text-[#16120f]' : 'text-[#16120f]/75 hover:bg-[#f6f1ea]' }}"
                        >
                            <span>{{ $cmsPage['label'] }}</span>
                            <svg class="h-4 w-4 shrink-0 transition" :class="openPage === @js($cmsPage['id']) ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <ul x-show="openPage === @js($cmsPage['id'])" x-cloak class="space-y-0.5 px-1 pb-2 pt-1">
                            @foreach ($cmsPage['sections'] as $sec)
                                <li>
                                    <button
                                        type="button"
                                        wire:click="selectSection('{{ $cmsPage['id'] }}', '{{ $sec['id'] }}')"
                                        class="flex w-full items-center gap-2 rounded-lg py-2 pl-6 pr-3 text-left text-sm transition {{ $page === $cmsPage['id'] && $section === $sec['id'] ? 'bg-[#16120f] font-medium text-white' : 'text-[#16120f]/65 hover:bg-white hover:text-[#16120f]' }}"
                                    >
                                        <span class="h-1 w-1 shrink-0 rounded-full {{ $page === $cmsPage['id'] && $section === $sec['id'] ? 'bg-[#e2c48a]' : 'bg-[#c4a574]/50' }}"></span>
                                        {{ $sec['label'] }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="mt-3 space-y-3 border-t border-black/5 pt-3">
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#16120f]/40">Services menu</p>
                        <a href="{{ route('app.packages.index') }}" class="flex w-full rounded-xl px-3 py-2.5 text-sm text-[#9b7b4b] hover:bg-[#f6f1ea]">
                            Edit services, prices & videos →
                        </a>
                    </div>
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#16120f]/40">Production menu</p>
                        <a href="{{ route('app.production.index') }}" class="flex w-full rounded-xl px-3 py-2.5 text-sm text-[#9b7b4b] hover:bg-[#f6f1ea]">
                            Edit production projects & films →
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        {{-- Editor panel --}}
        <div class="min-w-0 lg:col-span-9">
            <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">{{ $pageLabel }}</p>
                <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">{{ $sectionLabel }}</h2>
                <p class="mt-2 text-sm text-[#16120f]/60">
                    @if ($activeModule)
                        Manage entries shown on the live {{ strtolower($pageLabel) }}.
                    @else
                        Edit text and images for this section. Changes go live when you save.
                    @endif
                </p>
            </header>

            @if ($activeModule)
                @include('livewire.platform.partials.cms-modules')
            @elseif ($page === 'home' && $section === 'hero')
                @include('livewire.platform.partials.cms-hero-slider')
            @else
                <form wire:submit="saveCopy" class="space-y-6">
                    @if ($activeCopyRows->isEmpty())
                        <p class="rounded-[28px] bg-white p-8 text-sm text-[#16120f]/60 shadow-sm ring-1 ring-black/5">
                            No editable fields in this section yet. Run <code class="rounded bg-[#f6f1ea] px-1">php artisan migrate</code> if you recently updated the CMS.
                        </p>
                    @else
                        <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <div class="grid gap-5">
                                @foreach ($activeCopyRows as $row)
                                    <x-cms-field :row="$row" :value="data_get($fields, $row->key, '')" wire:key="cms-field-{{ $row->key }}" />
                                @endforeach
                            </div>
                        </section>
                        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white shadow-sm">
                            Save {{ $sectionLabel }}
                        </button>
                    @endif
                </form>
            @endif
        </div>
    </div>
</div>
