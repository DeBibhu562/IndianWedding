@extends('layouts.public')
@section('content')
{{-- Full-width hero --}}
<div class="relative left-1/2 w-screen max-w-[100vw] -translate-x-1/2 bg-[#0c0b0a] text-white">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_55%_at_50%_-20%,rgba(196,165,116,0.2),transparent)]"></div>
    <div class="relative mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8 lg:py-24">
        <p class="text-sm uppercase tracking-[0.28em] text-[#c4a574]">{{ site('gallery.eyebrow', 'Portfolio') }}</p>
        <h1 class="mt-4 font-[Cormorant_Garamond] text-4xl leading-[1.05] sm:text-5xl md:text-6xl lg:text-7xl">{{ site('gallery.heading', 'Gallery') }}</h1>
        <p class="mt-5 max-w-3xl text-base leading-relaxed text-white/72 md:text-lg">{{ site('gallery.intro') }}</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="/book-consultation" class="inline-flex rounded-full bg-[#c4a574] px-6 py-3 text-sm font-medium text-[#16120f] transition hover:bg-[#d4b888]">{{ site('home.cta', 'Book Consultant') }}</a>
            <a href="/services" class="inline-flex rounded-full border border-white/20 px-6 py-3 text-sm text-white transition hover:bg-white/10">Our services →</a>
        </div>
    </div>
</div>

<section class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 md:py-20 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h2 class="font-[Cormorant_Garamond] text-3xl md:text-4xl">{{ site('gallery.prewedding_heading', 'Pre-Wedding Shoot') }}</h2>
        @if ($portfolio->isNotEmpty())
            <p class="text-sm text-[#16120f]/50">{{ $portfolio->count() }} {{ $portfolio->count() === 1 ? 'image' : 'images' }}</p>
        @endif
    </div>

    @if ($portfolio->isEmpty())
        <p class="mt-10 rounded-[28px] bg-white px-8 py-12 text-center text-sm text-[#16120f]/60 shadow-sm ring-1 ring-black/5">
            Gallery images will appear here once added in Website CMS → Gallery.
        </p>
    @else
        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-7 xl:gap-8">
            @foreach ($portfolio as $item)
                <a href="{{ $item->publicUrl() }}" class="group block overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="relative aspect-[4/5] overflow-hidden bg-[#f0ebe3] sm:aspect-[3/4] lg:aspect-[4/5]">
                        <img
                            src="{{ $item->imageUrl() }}"
                            alt="{{ $item->title }}"
                            class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                            loading="lazy"
                        >
                    </div>
                    <figcaption class="px-5 py-4">
                        <p class="font-[Cormorant_Garamond] text-xl leading-tight group-hover:text-[#9b7b4b]">{{ $item->title }}</p>
                        @if ($item->category)
                            <p class="mt-1 text-xs uppercase tracking-[0.14em] text-[#16120f]/45">{{ $item->category }}</p>
                        @endif
                    </figcaption>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection
