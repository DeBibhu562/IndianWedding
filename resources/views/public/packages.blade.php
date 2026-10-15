@extends('layouts.public')
@section('content')
<section class="mx-auto max-w-6xl px-6 py-20">
    <p class="text-sm uppercase tracking-[0.25em] text-[#9b7b4b]">{{ site('packages.eyebrow', 'Pricing') }}</p>
    <h1 class="mt-4 font-[Cormorant_Garamond] text-5xl">{{ site('packages.heading', 'Wedding Photography & Videography Packages') }}</h1>
    <p class="mt-4 max-w-3xl text-lg opacity-70">{{ site('packages.intro') }}</p>

    {{-- Quick links --}}
    <div class="mt-10 flex flex-wrap gap-3">
        <a href="/services" class="inline-flex items-center gap-2 rounded-full bg-[#16120f] px-5 py-2.5 text-sm text-white transition hover:bg-[#2a241f]">
            {{ site('packages.link_services', 'All services') }}
            <span aria-hidden="true">→</span>
        </a>
        <a href="/production" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">
            {{ site('packages.link_production', 'Production & films') }}
            <span aria-hidden="true">→</span>
        </a>
        <a href="/book-consultation" class="inline-flex items-center gap-2 rounded-full bg-[#c4a574] px-5 py-2.5 text-sm text-[#16120f] transition hover:bg-[#d4b888]">
            {{ site('home.cta', 'Book Consultant') }}
        </a>
    </div>

    {{-- Wedding packages --}}
    <div class="mt-16">
        <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('packages.wedding_heading', 'Wedding packages') }}</h2>
        <p class="mt-2 max-w-2xl text-sm opacity-60">{{ site('packages.wedding_intro') }}</p>
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            @forelse ($packages as $package)
                <x-public.package-card :package="$package" :feature-limit="6" />
            @empty
                <p class="col-span-full text-sm opacity-60">{{ site('packages.empty_wedding', 'Wedding packages will appear here once added in the studio.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Services preview --}}
    @if (($services ?? collect())->isNotEmpty())
        <div class="mt-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('packages.services_heading', site('services.heading', 'Our Services')) }}</h2>
                    <p class="mt-2 max-w-2xl text-sm opacity-60">{{ site('packages.services_intro', site('services.intro')) }}</p>
                </div>
                <a href="/services" class="shrink-0 rounded-full bg-white px-5 py-2.5 text-sm font-medium ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">
                    {{ site('packages.services_link', 'View all services →') }}
                </a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services->take(6) as $service)
                    <x-public.package-card :package="$service" :show-price="false" :feature-limit="3" compact />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Production preview --}}
    @if (($productionProjects ?? collect())->isNotEmpty() || ($productionPackages ?? collect())->isNotEmpty())
        <div class="mt-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('packages.production_heading', site('production.heading', 'Production & Creative Services')) }}</h2>
                    <p class="mt-2 max-w-2xl text-sm opacity-60">{{ site('packages.production_intro', site('production.intro')) }}</p>
                </div>
                <a href="/production" class="shrink-0 rounded-full bg-white px-5 py-2.5 text-sm font-medium ring-1 ring-black/10 transition hover:bg-[#f6f1ea]">
                    {{ site('packages.production_link', 'View all production →') }}
                </a>
            </div>

            @if (($productionProjects ?? collect())->isNotEmpty())
                <h3 class="mt-10 text-sm font-semibold uppercase tracking-[0.18em] text-[#9b7b4b]">{{ site('packages.production_featured_heading', 'Featured projects') }}</h3>
                <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($productionProjects as $project)
                        <x-public.production-project-card :project="$project" />
                    @endforeach
                </div>
            @endif

            @if (($productionPackages ?? collect())->isNotEmpty())
                <h3 class="mt-10 text-sm font-semibold uppercase tracking-[0.18em] text-[#9b7b4b]">{{ site('packages.production_services_heading', 'Production services') }}</h3>
                <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($productionPackages->take(6) as $productionPackage)
                        <x-public.package-card :package="$productionPackage" :show-price="false" :feature-limit="4" />
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Add-ons --}}
    @if (($addons ?? collect())->isNotEmpty())
        <div class="mt-20">
            <h2 class="font-[Cormorant_Garamond] text-4xl">{{ site('packages.addons_heading', 'Wedding Add-On Services') }}</h2>
            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($addons as $addon)
                    <div class="rounded-2xl bg-white px-5 py-4 text-sm shadow-sm ring-1 ring-black/5">{{ $addon->name }}</div>
                @endforeach
            </div>
            @if (site('packages.addons_note'))
                <p class="mt-6 text-sm opacity-60">{{ site('packages.addons_note') }}</p>
            @endif
        </div>
    @endif
</section>
@endsection
