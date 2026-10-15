@extends('layouts.public')
@section('content')
{{-- Full-width hero --}}
<div class="relative left-1/2 w-screen max-w-[100vw] -translate-x-1/2 bg-[#0c0b0a] text-white">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_70%_-10%,rgba(196,165,116,0.22),transparent)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_50%_40%_at_0%_100%,rgba(196,165,116,0.08),transparent)]"></div>
    <div class="relative mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8 lg:py-24">
        <div class="grid items-end gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7 xl:col-span-8">
                <p class="text-sm uppercase tracking-[0.28em] text-[#c4a574]">{{ site('production.eyebrow', 'Production & Creative Services') }}</p>
                <h1 class="mt-4 font-[Cormorant_Garamond] text-4xl leading-[1.05] sm:text-5xl md:text-6xl lg:text-[3.35rem] xl:text-7xl">{{ site('production.heading', 'Production & Creative Services') }}</h1>
                <p class="mt-5 max-w-3xl text-base leading-relaxed text-white/72 md:text-lg">{{ site('production.intro') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row lg:col-span-5 lg:flex-col xl:col-span-4">
                <a href="/book-consultation" class="inline-flex items-center justify-center rounded-full bg-[#c4a574] px-6 py-3.5 text-sm font-medium text-[#16120f] transition hover:bg-[#d4b888]">
                    {{ site('home.cta', 'Book Consultant') }}
                </a>
                <a href="/packages" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-sm text-white transition hover:bg-white/10">
                    Wedding packages & pricing →
                </a>
                <a href="/services" class="inline-flex items-center justify-center rounded-full border border-white/10 px-6 py-3.5 text-sm text-white/85 transition hover:bg-white/5">
                    All services →
                </a>
            </div>
        </div>
    </div>
</div>

@if ($productionProjects->isNotEmpty())
    <section class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="font-[Cormorant_Garamond] text-3xl md:text-4xl">{{ site('production.featured_heading', 'Featured projects') }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-[#16120f]/60">{{ site('production.featured_intro', 'Real wedding films, music videos and event productions from our portfolio.') }}</p>
            </div>
        </div>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4 xl:gap-7">
            @foreach ($productionProjects as $item)
                <x-public.production-project-card :project="$item" />
            @endforeach
        </div>
    </section>
@endif

<section class="relative left-1/2 w-screen max-w-[100vw] -translate-x-1/2 bg-[#f0ebe3]">
    <div class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="font-[Cormorant_Garamond] text-3xl md:text-4xl lg:text-5xl">{{ site('production.services_heading', 'What we produce') }}</h2>
            <p class="max-w-md text-sm text-[#16120f]/55">End-to-end packages for film, episodic, corporate and product campaigns.</p>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 xl:gap-8">
            @forelse ($packages as $package)
                <x-public.package-card :package="$package" :show-price="false" :feature-limit="5" />
            @empty
                <p class="col-span-full text-sm opacity-60">Production packages will appear here once added in the studio.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-12 lg:gap-10 xl:gap-14">
        <div class="lg:col-span-4 xl:col-span-3">
            <h2 class="font-[Cormorant_Garamond] text-3xl md:text-4xl">{{ site('production.team_heading', 'Production Team') }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#16120f]/60">Specialists across direction, camera, aerial, post and sound — one crew for concept through delivery.</p>
        </div>
        <ul class="grid gap-3 sm:grid-cols-2 lg:col-span-8 lg:grid-cols-2 xl:col-span-9 xl:grid-cols-3 xl:gap-4">
            @foreach (range(1, 9) as $n)
                @if (site('production.team_'.$n))
                    <li class="rounded-2xl bg-white px-5 py-4 text-sm leading-relaxed shadow-sm ring-1 ring-black/5">{{ site('production.team_'.$n) }}</li>
                @endif
            @endforeach
        </ul>
    </div>
</section>

<section class="relative left-1/2 w-screen max-w-[100vw] -translate-x-1/2 border-y border-black/5 bg-white">
    <div class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
        <h2 class="text-center font-[Cormorant_Garamond] text-3xl md:text-4xl">{{ site('production.workflow_heading', 'Production Workflow') }}</h2>
        <p class="mx-auto mt-3 max-w-2xl text-center text-sm text-[#16120f]/55">From first brief to final master — a clear path for every production.</p>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 md:gap-5 lg:grid-cols-5 lg:gap-4 xl:gap-6">
            @foreach (range(1, 5) as $n)
                @if (site('production.step_'.$n.'_title'))
                    <article class="relative flex h-full flex-col rounded-[28px] bg-[#f6f1ea] p-6 ring-1 ring-black/[0.04] lg:p-5 xl:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">{{ str_pad((string) $n, 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mt-3 font-[Cormorant_Garamond] text-xl leading-tight xl:text-2xl">{{ site('production.step_'.$n.'_title') }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-[#16120f]/70">{{ site('production.step_'.$n.'_body') }}</p>
                    </article>
                @endif
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
    <div class="relative overflow-hidden rounded-[32px] bg-[#16120f] px-6 py-12 text-center text-white sm:px-10 sm:py-14 md:px-16 md:py-16 lg:px-20">
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#c4a574]/15 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-[#c4a574]/10 blur-3xl"></div>
        <div class="relative">
            <p class="font-[Cormorant_Garamond] text-3xl text-[#e2c48a] md:text-4xl lg:text-5xl">{{ site('production.tagline', 'Your Story. Our Lens. Forever.') }}</p>
            <p class="mx-auto mt-5 max-w-3xl text-sm leading-relaxed text-white/75 md:text-base">{{ site('production.tagline_body') }}</p>
            <a href="/book-consultation" class="mt-8 inline-block rounded-full bg-[#c4a574] px-10 py-3.5 text-sm font-medium text-[#16120f] transition hover:bg-[#d4b888]">{{ site('home.cta', 'BOOK CONSULTANT') }}</a>
        </div>
    </div>
</section>
@endsection
