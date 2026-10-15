<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? site('seo.title', 'Unik Studio') }}</title>
    <meta name="description" content="{{ $description ?? site('seo.description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? site('seo.title', 'Unik Studio') }}">
    <meta property="og:description" content="{{ $description ?? site('seo.description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => site('brand.name', $organization->name ?? 'Unik Studio'),
            'url' => url('/'),
            'telephone' => site('contact.phone', $organization->phone ?? ''),
            'email' => site('contact.emails', $organization->email ?? ''),
            'address' => site('contact.office'),
            'logo' => studio_logo(),
            'areaServed' => 'India',
        ];
        $socialUrls = array_column(social_links(), 'url');
        if ($socialUrls !== []) {
            $schema['sameAs'] = $socialUrls;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700|outfit:300,400,500,600" rel="stylesheet" />
    @include('partials.brand-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f6f1ea] font-[Outfit] text-[#16120f]">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 z-50 bg-white px-3 py-2">Skip to content</a>
    @php
        $navLinks = [
            ['/', site('nav.home', 'Home'), request()->is('/')],
            ['/about', site('nav.about', 'About'), request()->is('about')],
            ['/services', site('nav.services', 'Services'), request()->is('services') || request()->is('services/*')],
            ['/packages', site('nav.packages', 'Packages'), request()->is('packages*')],
            ['/production', site('nav.production', 'Production'), request()->is('production*')],
            ['/gallery', site('nav.gallery', 'Gallery'), request()->is('gallery') || request()->is('portfolio*') || request()->is('projects*')],
            ['/contact', site('nav.contact', 'Contact'), request()->is('contact')],
        ];
        $loginActive = request()->routeIs('login', 'password.*');
        $loginHref = route('login');
        $loginLabel = site('nav.login', 'Login');
        if (auth()->check()) {
            $loginOrg = $organization ?? \App\Models\Organization::query()->where('is_active', true)->first();
            $loginRole = $loginOrg ? auth()->user()->roleIn($loginOrg) : null;
            $loginHref = route($loginRole?->dashboardRoute() ?? 'app.dashboard');
            $loginLabel = site('nav.dashboard', 'Dashboard');
            $loginActive = request()->routeIs('app.*', 'client.*');
        }
    @endphp
    <header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-40 bg-[#0c0b0a]/95 text-white shadow-[0_10px_40px_rgba(0,0,0,0.35)] backdrop-blur-xl">
        <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-3 px-4 py-3 sm:px-5 md:px-6">
            <a href="/" class="inline-flex shrink-0 items-center">
                <x-brand-logo class="h-8 max-w-[140px] sm:h-9 sm:max-w-[168px] 2xl:h-10 2xl:max-w-[196px]" />
            </a>
            <nav class="hidden shrink-0 items-center justify-end gap-0.5 text-[12px] font-medium tracking-wide 2xl:flex 2xl:gap-1 2xl:text-[13px]" aria-label="Main">
                @foreach ($navLinks as [$href, $label, $active])
                    <a href="{{ $href }}" class="rounded-full px-2.5 py-1.5 transition 2xl:px-3 2xl:py-2 {{ $active ? 'bg-white/10 text-[#e2c48a]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">{{ $label }}</a>
                @endforeach
                <a href="{{ $loginHref }}" class="ml-1 shrink-0 rounded-full border border-white/20 px-3 py-1.5 transition 2xl:ml-2 2xl:px-3 2xl:py-2 {{ $loginActive ? 'bg-white/10 text-[#e2c48a]' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">{{ $loginLabel }}</a>
                <a href="/book-consultation" class="ml-1 shrink-0 rounded-full bg-[#c4a574] px-3 py-1.5 text-[#16120f] shadow-sm transition hover:bg-[#d4b888] 2xl:ml-1 2xl:px-4 2xl:py-2">{{ site('nav.reservation', 'Book Consultant') }}</a>
            </nav>
            <div class="flex shrink-0 items-center gap-2 2xl:hidden">
                <a href="{{ $loginHref }}" class="inline-flex h-10 items-center rounded-full border border-white/20 px-3.5 text-xs font-medium text-white/85 transition hover:bg-white/5 hover:text-white sm:px-4 sm:text-sm {{ $loginActive ? 'bg-white/10 text-[#e2c48a]' : '' }}">{{ $loginLabel }}</a>
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-white" @click="open=!open" :aria-expanded="open" :aria-label="open ? 'Close menu' : 'Open menu'">
                <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
        </div>
        <div x-show="open" x-cloak x-transition class="border-t border-white/10 bg-[#0c0b0a] px-5 py-4 2xl:hidden">
            <div class="flex flex-col gap-1 text-sm">
                @foreach ($navLinks as [$href, $label, $active])
                    <a href="{{ $href }}" class="rounded-xl px-3 py-2.5 {{ $active ? 'bg-white/10 text-[#e2c48a]' : 'text-white/80' }}">{{ $label }}</a>
                @endforeach
                <a href="/faq" class="rounded-xl px-3 py-2.5 text-white/80">{{ site('nav.faq', 'FAQ') }}</a>
                <a href="{{ $loginHref }}" class="rounded-xl px-3 py-2.5 {{ $loginActive ? 'bg-white/10 text-[#e2c48a]' : 'text-white/80' }}">{{ $loginLabel }}</a>
                <a href="/book-consultation" class="mt-2 rounded-full bg-[#c4a574] px-4 py-3 text-center font-medium text-[#16120f]">{{ site('nav.reservation', 'Book Consultant') }}</a>
            </div>
        </div>
    </header>
    <main id="content" class="pt-[68px]">
        <x-swal-flash />
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    <footer class="mt-24 bg-[#0c0b0a] text-white">
        <div class="mx-auto max-w-[1400px] px-4 py-16 sm:px-5 md:px-6 lg:px-8 lg:py-20">
            <div class="grid gap-12 md:grid-cols-12 md:gap-10 lg:gap-16 xl:gap-20">
                <div class="md:col-span-12 lg:col-span-5 xl:col-span-5">
                    <a href="/" class="inline-flex items-center">
                        <x-brand-logo class="h-14 max-w-[280px] lg:h-16 lg:max-w-[300px]" />
                    </a>
                    <p class="mt-5 max-w-xl text-sm leading-relaxed text-white/60 lg:text-[15px] lg:leading-relaxed">{{ site('footer.blurb') }}</p>
                    <x-social-links class="mt-6" />
                    <a href="/book-consultation" class="mt-6 inline-flex rounded-full bg-[#c4a574] px-5 py-2.5 text-sm font-medium text-[#16120f] transition hover:bg-[#d4b888]">{{ site('nav.reservation', 'Book Consultant') }}</a>
                </div>
                <div class="md:col-span-6 lg:col-span-3 lg:col-start-7 xl:col-start-7">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#c4a574]">{{ site('footer.links_heading', 'Links') }}</p>
                    <div class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2.5 text-sm text-white/70 sm:grid-cols-1">
                        <a class="transition hover:text-white" href="/">{{ site('nav.home', 'Home') }}</a>
                        <a class="transition hover:text-white" href="/about">{{ site('nav.about', 'About us') }}</a>
                        <a class="transition hover:text-white" href="/services">{{ site('nav.services', 'Our Services') }}</a>
                        <a class="transition hover:text-white" href="/packages">{{ site('nav.packages', 'Packages') }}</a>
                        <a class="transition hover:text-white" href="/production">{{ site('nav.production', 'Production') }}</a>
                        <a class="transition hover:text-white" href="/gallery">{{ site('nav.gallery', 'Gallery') }}</a>
                        <a class="transition hover:text-white" href="/testimonials">{{ site('nav.testimonials', 'Feedbacks') }}</a>
                        <a class="transition hover:text-white" href="/contact">{{ site('nav.contact', 'Get in touch') }}</a>
                        <a class="transition hover:text-white" href="/login">Client / studio login</a>
                    </div>
                </div>
                <div class="md:col-span-6 lg:col-span-4 lg:col-start-10 xl:col-start-10">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#c4a574]">{{ site('footer.contact_heading', 'Contact Info') }}</p>
                    <div class="mt-4 space-y-3 text-sm leading-relaxed text-white/70 lg:text-[15px]">
                        <p>{{ site('contact.phone') }}</p>
                        <x-contact-emails />
                        <p class="max-w-md whitespace-pre-line">{{ site('contact.office') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-[1400px] flex-col gap-3 px-4 py-5 text-xs text-white/40 sm:px-5 md:flex-row md:items-center md:justify-between md:px-6 lg:px-8">
                <p class="md:max-w-[33%]">{{ site('footer.copyright') }}</p>
                <p class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 md:justify-center">
                    <a href="/terms-and-conditions" class="transition hover:text-white/70">Terms</a>
                    <span aria-hidden="true">·</span>
                    <a href="/privacy-policy" class="transition hover:text-white/70">Privacy</a>
                    <span aria-hidden="true">·</span>
                    <a href="/cookie-policy" class="transition hover:text-white/70">Cookies</a>
                </p>
                <p class="md:max-w-[33%] md:text-right">
                    Designed and Developed by
                    <a href="https://www.vedmint.com" target="_blank" rel="noopener noreferrer" class="text-[#c4a574] transition hover:text-[#e2c48a]">VedMint Consultancy Services</a>
                </p>
            </div>
        </div>
    </footer>
    @include('public.partials.lead-popup')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script>
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && window.gsap) {
            gsap.registerPlugin(ScrollTrigger);
            gsap.from('[data-reveal]', { y: 24, opacity: 0, duration: .8, stagger: .08, ease: 'power2.out' });
            gsap.utils.toArray('[data-scroll]').forEach((el) => {
                gsap.from(el, { scrollTrigger: { trigger: el, start: 'top 85%' }, y: 30, opacity: 0, duration: .9, ease: 'power2.out' });
            });
        }
    </script>
    @livewireScripts
</body>
</html>
