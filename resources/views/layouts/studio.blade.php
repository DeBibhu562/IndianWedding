<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Studio' }} · Unik Studio</title>
    @include('partials.brand-head')
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:600,700|outfit:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/studio.js'])
    @livewireStyles
</head>
<body class="h-screen overflow-hidden bg-[#f4eee6] font-[Outfit] text-[#16120f] antialiased" x-data="{ sidebar: false, userMenu: false }" @keydown.escape.window="sidebar=false; userMenu=false">
@php
    $user = auth()->user();
    $role = $user?->roleIn(\App\Support\Tenant::current());
    $initials = collect(explode(' ', (string) $user?->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    $nav = [
        'Overview' => [
            ['app.dashboard', 'Dashboard', 'home', 'app.dashboard'],
            ['app.reports.index', 'Reports', 'reports', 'app.reports.*', 'reports.view'],
            ['app.calendar', 'Calendar', 'calendar', 'app.calendar'],
        ],
        'Pipeline' => [
            ['app.leads.index', 'Leads', 'leads', 'app.leads.index|app.leads.show|app.leads.create'],
            ['app.leads.pipeline', 'Pipeline', 'pipeline', 'app.leads.pipeline'],
            ['app.consultations.index', 'Consultations', 'consult', 'app.consultations.*'],
        ],
        'Operations' => [
            ['app.clients.index', 'Clients', 'clients', 'app.clients.*'],
            ['app.projects.index', 'Projects', 'projects', 'app.projects.*'],
            ['app.tasks.index', 'Tasks', 'tasks', 'app.tasks.*'],
            ['app.team.index', 'Team', 'team', 'app.team.index|app.team.create|app.team.edit'],
            ['app.team.roles', 'Roles', 'roles', 'app.team.roles|app.team.roles.create|app.team.roles.edit|app.team.permissions'],
        ],
        'Finance' => [
            ['app.payments.index', 'Payments', 'payments', 'app.payments.*'],
            ['app.invoices.index', 'Invoices', 'invoices', 'app.invoices.*'],
        ],
        'Delivery' => [
            ['app.files.index', 'Files', 'files', 'app.files.*'],
        ],
        'Services' => [
            ['app.packages.index', 'Packages & services', 'packages', 'app.packages.*'],
        ],
        'Production' => [
            ['app.production.index', 'Creative projects', 'packages', 'app.production.*'],
        ],
        'Studio' => [
            ['app.website', 'Website CMS', 'website', 'app.website'],
            ['app.settings.index', 'Studio profile', 'settings', 'app.settings.index'],
            ['app.settings.payments', 'Payment gateway', 'payments', 'app.settings.payments', 'settings.manage'],
            ['app.settings.email', 'Email / SMTP', 'mail', 'app.settings.email', 'settings.manage'],
            ['app.settings.google-drive', 'Google Drive', 'drive', 'app.settings.google-drive', 'settings.manage'],
        ],
    ];
@endphp

<div class="flex h-full min-h-0">
    <div x-show="sidebar" x-cloak class="fixed inset-0 z-40 bg-[#16120f]/50 md:hidden" @click="sidebar=false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex h-full max-h-screen w-[272px] flex-col overflow-hidden border-r border-white/5 bg-[#14110e] text-[#f6f1ea] transition-transform duration-200 md:static md:shrink-0 md:translate-x-0"
        :class="sidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >
        <div class="flex shrink-0 items-center justify-between px-5 py-5">
            <a href="{{ route('app.dashboard') }}" class="block min-w-0">
                <x-brand-logo class="h-11 max-w-[180px]" />
            </a>
            <button type="button" class="rounded-lg p-2 text-white/70 md:hidden" @click="sidebar=false" aria-label="Close menu">
                <x-studio.icon name="close" />
            </button>
        </div>

        <nav class="studio-scroll min-h-0 flex-1 space-y-5 overflow-y-auto overscroll-contain px-3 pb-4">
            @foreach ($nav as $group => $links)
                <div>
                    <p class="px-3 pb-1.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">{{ $group }}</p>
                    <div class="space-y-0.5">
                        @foreach ($links as $link)
                            @php
                                [$route, $label, $icon, $match] = $link;
                                $permission = $link[4] ?? null;
                            @endphp
                            @continue($permission && ! $user?->canInOrganization($permission, \App\Support\Tenant::current()))
                            @continue($route === 'app.team.roles' && ! ($user?->can('create', \App\Models\OrganizationUser::class) || $user?->canInOrganization('permissions.manage', \App\Support\Tenant::current())))
                            @php $active = collect(explode('|', $match))->contains(fn ($pattern) => request()->routeIs($pattern)); @endphp
                            <a href="{{ route($route) }}" @click="sidebar=false" class="flex items-center gap-3 rounded-xl py-2.5 text-sm transition {{ $active ? 'border-l-2 border-[#c4a574] bg-white/10 pl-[10px] pr-3 text-[#e2c48a]' : 'border-l-2 border-transparent px-3 text-white/70 hover:bg-white/5 hover:text-white' }}">
                                <x-studio.icon :name="$icon" class="h-[18px] w-[18px]" />
                                <span>{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-white/10 bg-[#14110e] p-3">
            <a href="{{ route('profile.edit') }}" @click="sidebar=false" class="flex items-center gap-3 rounded-2xl bg-white/5 px-3 py-3 transition hover:bg-white/10">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#c4a574] text-sm font-semibold text-[#16120f]">{{ $initials }}</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ $user?->name }}</p>
                    <p class="truncate text-xs text-white/45">{{ $role?->label() ?? 'Staff' }} · Edit profile</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-white/55 transition hover:bg-white/5 hover:text-white">
                    <x-studio.icon name="logout" class="h-4 w-4" /> Sign out
                </button>
            </form>
        </div>
    </aside>

    <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-y-auto overscroll-contain">
        <header class="sticky top-0 z-30 flex shrink-0 items-center gap-3 border-b border-[#16120f]/8 bg-[#f4eee6]/90 px-4 py-3 backdrop-blur-xl md:px-8">
            <button type="button" class="rounded-xl bg-white p-2 shadow-sm md:hidden" @click="sidebar=true" aria-label="Open menu">
                <x-studio.icon name="menu" />
            </button>
            <div class="min-w-0 flex-1">
                <livewire:studio.search />
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('app.leads.create') }}" class="hidden items-center gap-1.5 rounded-xl bg-[#16120f] px-3 py-2 text-sm font-medium text-white shadow-sm sm:inline-flex">
                    <x-studio.icon name="plus" class="h-4 w-4" /> New lead
                </a>
                <a href="/" target="_blank" class="hidden rounded-xl bg-white p-2 text-[#16120f]/70 shadow-sm hover:text-[#16120f] md:inline-flex" title="View website">
                    <x-studio.icon name="website" />
                </a>
                <div class="relative">
                    <button type="button" @click="userMenu=!userMenu" class="flex items-center gap-2 rounded-xl bg-white px-2 py-1.5 shadow-sm">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#16120f] text-xs font-semibold text-[#e2c48a]">{{ $initials }}</span>
                        <span class="hidden pr-1 text-sm md:inline">{{ $user?->name }}</span>
                    </button>
                    <div x-show="userMenu" x-cloak @click.outside="userMenu=false" class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl bg-white py-1 text-sm shadow-xl ring-1 ring-black/5">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 hover:bg-[#f6f1ea]">My profile</a>
                        <a href="{{ route('app.settings.index') }}" class="block px-4 py-2.5 hover:bg-[#f6f1ea]">Settings</a>
                        <a href="{{ route('app.website') }}" class="block px-4 py-2.5 hover:bg-[#f6f1ea]">Website CMS</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full px-4 py-2.5 text-left hover:bg-[#f6f1ea]">Sign out</button></form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 bg-[#f4eee6] px-4 py-6 md:px-8 md:py-8">
            <x-swal-flash />
            {{ $slot }}
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@if (request()->routeIs('app.calendar'))
    @vite('resources/js/calendar.js')
@endif
@stack('vite')
@livewireScripts
</body>
</html>
