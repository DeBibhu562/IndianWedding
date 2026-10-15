@php
    use App\Support\UnikStudioAssets;

    $defaultImages = [
        1 => UnikStudioAssets::url('service-wedding.jpg'),
        2 => UnikStudioAssets::url('service-prewedding.jpg'),
        3 => UnikStudioAssets::url('service-candid.jpg'),
        4 => UnikStudioAssets::url('service-cinema.jpg'),
        5 => UnikStudioAssets::url('service-birthday.jpg'),
    ];

    $slides = [];

    for ($n = 1; $n <= 5; $n++) {
        $slides[] = [
            'num' => str_pad((string) $n, 2, '0', STR_PAD_LEFT),
            'tab' => site("home.slide_{$n}_tab"),
            'title' => site("home.slide_{$n}_title"),
            'description' => site("home.slide_{$n}_body"),
            'cta' => site("home.slide_{$n}_cta"),
            'href' => site("home.slide_{$n}_href"),
            'image' => site("home.slide_{$n}_image") ?: ($defaultImages[$n] ?? ''),
        ];
    }
@endphp

<section
    class="home-hero-slider relative isolate -mt-[68px] min-h-[100svh] overflow-hidden bg-[#0c0b0a] text-white"
    x-data="homeHeroSlider({ total: {{ count($slides) }}, duration: 6000 })"
    x-init="start()"
    @mouseenter="pause()"
    @mouseleave="resume()"
>
    <div class="absolute inset-0">
        @foreach ($slides as $i => $slide)
            <div
                class="absolute inset-0 transition-opacity duration-700 ease-out"
                :class="active === {{ $i }} ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
            >
                <img
                    src="{{ $slide['image'] }}"
                    alt="{{ $slide['title'] }}"
                    class="h-full w-full object-cover"
                    @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#0c0b0a]/92 via-[#0c0b0a]/55 to-[#0c0b0a]/20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0c0b0a] via-transparent to-[#0c0b0a]/40"></div>
            </div>
        @endforeach
    </div>

    <div class="relative z-10 flex min-h-[100svh] flex-col justify-end pb-44 pt-28 sm:pb-40 md:pb-44 md:pt-32">
        <div class="mx-auto w-full max-w-6xl px-5 md:px-8">
            @foreach ($slides as $i => $slide)
                <div
                    x-show="active === {{ $i }}"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-5"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 absolute"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="max-w-2xl"
                    @unless ($i === 0) x-cloak @endunless
                >
                    <p class="text-sm font-semibold tracking-[0.22em] text-[#c4a574]">{{ $slide['num'] }}</p>
                    <h1 class="mt-3 font-[Cormorant_Garamond] text-[2.35rem] leading-[1.08] text-balance text-white sm:text-5xl md:text-6xl lg:text-7xl">
                        {{ $slide['title'] }}
                    </h1>
                    <p class="mt-5 max-w-xl text-[15px] leading-relaxed text-white/78 sm:text-lg">
                        {{ $slide['description'] }}
                    </p>
                    <div class="mt-8">
                        <a
                            href="{{ $slide['href'] }}"
                            class="inline-flex items-center rounded-sm bg-[#c4a574] px-6 py-3 text-sm font-semibold text-[#16120f] transition hover:bg-[#d4b888]"
                        >
                            {{ $slide['cta'] }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <nav class="absolute inset-x-0 bottom-0 z-20 border-t border-white/10 bg-[#0c0b0a]/80 backdrop-blur-md" aria-label="Hero slides">
        <div class="mx-auto grid max-w-6xl grid-cols-5 divide-x divide-white/10">
            @foreach ($slides as $i => $slide)
                <button
                    type="button"
                    class="group relative min-w-0 px-2 py-3.5 text-left transition sm:px-3 md:px-5 md:py-5"
                    :class="active === {{ $i }} ? 'bg-white/[0.06]' : 'hover:bg-white/[0.03]'"
                    @click="go({{ $i }})"
                    :aria-current="active === {{ $i }} ? 'true' : null"
                >
                    <span
                        class="pointer-events-none absolute inset-x-0 top-0 h-[2px] origin-left scale-x-0 bg-[#c4a574]"
                        x-ref="bar{{ $i }}"
                    ></span>
                    <span
                        class="block text-[10px] font-semibold tracking-[0.12em] sm:text-[11px] sm:tracking-[0.14em]"
                        :class="active === {{ $i }} ? 'text-[#c4a574]' : 'text-white/35'"
                    >{{ $slide['num'] }}</span>
                    <span
                        class="mt-1 block truncate text-[10px] leading-snug sm:mt-1.5 sm:whitespace-normal sm:text-xs md:text-[13px]"
                        :class="active === {{ $i }} ? 'font-semibold text-white' : 'text-white/45'"
                    >{{ $slide['tab'] }}</span>
                </button>
            @endforeach
        </div>
    </nav>
</section>
