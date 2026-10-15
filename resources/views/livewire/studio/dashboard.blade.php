@php
    $hour = now('Asia/Kolkata')->hour;
    $hello = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = Str::before(auth()->user()->name, ' ');
    $today = now('Asia/Kolkata');
    $hasOverdue = $stats['overdue_payments'] > 0;
    $quickActions = [
        ['app.leads.create', 'New lead', 'plus', 'bg-[#16120f] text-white ring-[#16120f]'],
        ['app.leads.pipeline', 'Pipeline', 'pipeline', 'bg-white text-[#16120f] ring-black/[0.06]'],
        ['app.calendar', 'Calendar', 'calendar', 'bg-white text-[#16120f] ring-black/[0.06]'],
        ['app.projects.index', 'Projects', 'projects', 'bg-white text-[#16120f] ring-black/[0.06]'],
        ['app.payments.index', 'Payments', 'payments', 'bg-white text-[#16120f] ring-black/[0.06]'],
        ['app.website', 'Website', 'website', 'bg-white text-[#16120f] ring-black/[0.06]'],
    ];
    $kpis = [
        [
            'label' => 'New leads',
            'value' => $stats['new_leads'],
            'hint' => 'Awaiting first contact',
            'href' => route('app.leads.index'),
            'icon' => 'leads',
            'accent' => 'text-[#9b7b4b] bg-[#f6f1ea]',
        ],
        [
            'label' => 'Active projects',
            'value' => $stats['active_projects'],
            'hint' => $stats['upcoming_weddings'].' upcoming weddings',
            'href' => route('app.projects.index'),
            'icon' => 'projects',
            'accent' => 'text-[#16120f] bg-[#ebe4d8]',
        ],
        [
            'label' => 'This month',
            'value' => \App\Support\Money::format($stats['monthly_revenue']),
            'hint' => 'Collected in '.$today->format('M Y'),
            'href' => route('app.payments.index'),
            'icon' => 'payments',
            'accent' => 'text-emerald-800 bg-emerald-50',
        ],
        [
            'label' => 'Overdue',
            'value' => $stats['overdue_payments'],
            'hint' => \App\Support\Money::format($stats['pending_payments']).' still due',
            'href' => route('app.payments.index'),
            'icon' => 'invoices',
            'accent' => $hasOverdue ? 'text-rose-700 bg-rose-50' : 'text-[#16120f]/60 bg-[#f6f1ea]',
        ],
    ];
@endphp

<div class="mx-auto max-w-[1400px] space-y-8">
    {{-- Welcome hero --}}
    <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#16120f] via-[#1c1712] to-[#2a2218] px-6 py-7 text-[#f6f1ea] shadow-xl md:px-8 md:py-9">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#c4a574]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 left-1/3 h-40 w-40 rounded-full bg-[#c4a574]/5 blur-2xl"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/70 ring-1 ring-white/10">
                    <x-studio.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ $today->format('l, j F Y') }}
                </div>
                <h1 class="mt-4 font-[Cormorant_Garamond] text-4xl leading-tight md:text-5xl lg:text-[3.25rem]">
                    {{ $hello }}, {{ $firstName }}
                </h1>
                <p class="mt-3 max-w-lg text-sm leading-relaxed text-white/60 md:text-base">
                    Everything you need to run Unik Studio — enquiries, weddings, payments, and delivery in one calm workspace.
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="{{ route('app.leads.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#c4a574] px-5 py-2.5 text-sm font-semibold text-[#16120f] shadow-lg shadow-black/20 transition hover:bg-[#d4b584]">
                    <x-studio.icon name="plus" class="h-4 w-4" />
                    New lead
                </a>
                <a href="{{ route('app.consultations.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white ring-1 ring-white/15 transition hover:bg-white/15">
                    Consultations
                </a>
            </div>
        </div>
    </section>

    {{-- Quick actions --}}
    <section>
        <p class="dash-section-title mb-3 px-1">Quick actions</p>
        <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 sm:gap-3">
            @foreach ($quickActions as [$route, $label, $icon, $style])
                <a href="{{ route($route) }}" class="group flex flex-col items-center gap-2.5 rounded-2xl p-3 ring-1 transition hover:-translate-y-0.5 hover:shadow-md {{ $style }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-black/[0.04] transition group-hover:scale-105 {{ str_contains($style, 'bg-[#16120f]') ? 'bg-white/10' : 'bg-[#f6f1ea]' }}">
                        <x-studio.icon :name="$icon" class="h-[18px] w-[18px]" />
                    </span>
                    <span class="text-center text-[11px] font-medium leading-tight sm:text-xs">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- KPI cards --}}
    <section>
        <p class="dash-section-title mb-3 px-1">At a glance</p>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($kpis as $kpi)
                <a href="{{ $kpi['href'] }}" class="dash-card group flex flex-col p-5 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">{{ $kpi['label'] }}</p>
                        <span class="rounded-xl p-2 transition group-hover:scale-105 {{ $kpi['accent'] }}">
                            <x-studio.icon :name="$kpi['icon']" class="h-4 w-4" />
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight md:text-4xl">{{ $kpi['value'] }}</p>
                    <p class="mt-2 text-sm text-[#16120f]/50">{{ $kpi['hint'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Revenue + tasks row --}}
    <section class="grid gap-3 md:grid-cols-3">
        <div class="dash-card relative overflow-hidden p-5 md:col-span-1">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#16120f] to-[#2a2218]"></div>
            <div class="relative text-[#f6f1ea]">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-white/40">All-time revenue</p>
                <p class="mt-3 font-[Cormorant_Garamond] text-4xl text-[#e2c48a] md:text-5xl">{{ \App\Support\Money::format($stats['revenue']) }}</p>
                <p class="mt-2 text-sm text-white/50">{{ $stats['conversion'] }}% conversion · {{ $stats['total_leads'] }} total leads</p>
                <a href="{{ route('app.reports.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-[#e2c48a] hover:text-white">
                    View reports <x-studio.icon name="arrow" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>
        <a href="{{ route('app.tasks.index') }}" class="dash-card group flex flex-col justify-between p-5 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">Open tasks</p>
                <x-studio.icon name="tasks" class="h-5 w-5 text-[#16120f]/30 transition group-hover:text-[#9b7b4b]" />
            </div>
            <div>
                <p class="text-4xl font-semibold tracking-tight">{{ $stats['open_tasks'] }}</p>
                <p class="mt-2 text-sm text-[#9b7b4b]">Go to task board →</p>
            </div>
        </a>
        <a href="{{ route('app.leads.pipeline') }}" class="dash-card group flex flex-col justify-between p-5 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">Pipeline</p>
                <x-studio.icon name="pipeline" class="h-5 w-5 text-[#16120f]/30 transition group-hover:text-[#9b7b4b]" />
            </div>
            <div>
                <p class="text-4xl font-semibold tracking-tight">{{ $stats['total_leads'] }}</p>
                <p class="mt-2 text-sm text-[#9b7b4b]">Manage lead stages →</p>
            </div>
        </a>
    </section>

    {{-- Main workboard --}}
    <section class="grid gap-6 xl:grid-cols-12">
        {{-- Chart --}}
        <div class="dash-card p-6 xl:col-span-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="dash-section-title">Operations</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl text-[#16120f]">Wedding projects by stage</h2>
                </div>
                <a href="{{ route('app.projects.index') }}" class="rounded-full bg-[#f6f1ea] px-4 py-1.5 text-sm font-medium text-[#9b7b4b] transition hover:bg-[#ebe4d8]">
                    All projects
                </a>
            </div>
            <div class="mt-6 min-h-[220px]">
                <canvas id="projectsChart" height="200"></canvas>
            </div>
        </div>

        {{-- Needs attention --}}
        <div class="dash-card flex flex-col p-6 xl:col-span-5 {{ $hasOverdue ? 'ring-rose-200/80' : '' }}">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="dash-section-title">Priority</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl text-[#16120f]">Needs attention</h2>
                </div>
                @if ($hasOverdue)
                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">{{ $stats['overdue_payments'] }} overdue</span>
                @endif
            </div>
            <ul class="mt-5 flex-1 space-y-2">
                @forelse ($stats['overdue_list'] as $item)
                    <li>
                        <a href="{{ route('app.payments.index') }}" class="flex items-center justify-between gap-3 rounded-xl bg-[#faf8f5] px-4 py-3 transition hover:bg-[#f6f1ea]">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ $item->project?->title ?? $item->name ?? 'Payment' }}</p>
                                <p class="text-xs text-[#16120f]/45">Due {{ optional($item->due_date)?->format('j M') ?? 'soon' }}</p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-rose-50 px-2.5 py-1 text-sm font-semibold text-rose-700">{{ \App\Support\Money::format($item->amount) }}</span>
                        </a>
                    </li>
                @empty
                    <li class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#16120f]/10 bg-[#faf8f5] px-6 py-10 text-center">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            <x-studio.icon name="tasks" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-sm font-medium text-[#16120f]/70">All caught up</p>
                        <p class="mt-1 text-xs text-[#16120f]/45">No overdue payments right now.</p>
                    </li>
                @endforelse
            </ul>
            <a href="{{ route('app.payments.index') }}" class="mt-4 text-center text-sm font-medium text-[#9b7b4b] hover:text-[#16120f]">View all payments →</a>
        </div>
    </section>

    {{-- Lists row --}}
    <section class="grid gap-6 lg:grid-cols-3">
        {{-- Upcoming weddings --}}
        <div class="dash-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="dash-section-title">Schedule</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-xl text-[#16120f]">Upcoming weddings</h2>
                </div>
                <a href="{{ route('app.calendar') }}" class="text-sm font-medium text-[#9b7b4b]">Calendar</a>
            </div>
            <ul class="mt-5 space-y-1">
                @forelse ($stats['upcoming_list'] as $project)
                    <li>
                        <a href="{{ route('app.projects.show', $project) }}" class="group flex items-center gap-3 rounded-xl px-2 py-3 transition hover:bg-[#faf8f5]">
                            <span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-[#16120f] text-[#e2c48a]">
                                <span class="text-[10px] font-semibold uppercase leading-none">{{ optional($project->wedding_date)?->format('M') }}</span>
                                <span class="text-sm font-bold leading-tight">{{ optional($project->wedding_date)?->format('j') }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium group-hover:text-[#9b7b4b]">{{ $project->title }}</span>
                                <span class="block truncate text-xs text-[#16120f]/45">{{ $project->customer?->name }} · {{ $project->city ?: $project->venue ?: 'TBC' }}</span>
                            </span>
                            <x-studio.icon name="arrow" class="h-4 w-4 shrink-0 text-[#16120f]/20 transition group-hover:text-[#9b7b4b]" />
                        </a>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-[#16120f]/10 px-4 py-8 text-center text-sm text-[#16120f]/45">
                        No upcoming dates. Add wedding dates to projects.
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- New enquiries --}}
        <div class="dash-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="dash-section-title">Pipeline</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-xl text-[#16120f]">New enquiries</h2>
                </div>
                <a href="{{ route('app.leads.index') }}" class="text-sm font-medium text-[#9b7b4b]">All leads</a>
            </div>
            <ul class="mt-5 space-y-1">
                @forelse ($stats['new_leads_list'] as $lead)
                    <li>
                        <a href="{{ route('app.leads.show', $lead) }}" class="group flex items-center justify-between gap-3 rounded-xl px-2 py-3 transition hover:bg-[#faf8f5]">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#f6f1ea] text-xs font-semibold text-[#9b7b4b]">
                                    {{ strtoupper(mb_substr($lead->name, 0, 1)) }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-medium group-hover:text-[#9b7b4b]">{{ $lead->name }}</span>
                                    @if ($lead->city)
                                        <span class="block truncate text-xs text-[#16120f]/45">{{ $lead->city }}</span>
                                    @endif
                                </span>
                            </span>
                            <span class="shrink-0 text-xs text-[#16120f]/40">{{ $lead->created_at->diffForHumans(null, true) }}</span>
                        </a>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-[#16120f]/10 px-4 py-8 text-center">
                        <p class="text-sm text-[#16120f]/45">No new leads yet.</p>
                        <a href="{{ route('app.leads.create') }}" class="mt-3 inline-block text-sm font-medium text-[#9b7b4b]">Add a lead →</a>
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Recent activity --}}
        <div class="dash-card p-6">
            <div>
                <p class="dash-section-title">Activity</p>
                <h2 class="mt-1 font-[Cormorant_Garamond] text-xl text-[#16120f]">Recent updates</h2>
            </div>
            <ul class="mt-5 space-y-0">
                @forelse ($stats['recent_activity'] as $item)
                    <li class="relative flex gap-3 pb-5 last:pb-0">
                        @if (! $loop->last)
                            <span class="absolute left-[7px] top-4 h-full w-px bg-[#16120f]/8"></span>
                        @endif
                        <span class="relative z-10 mt-1.5 h-3.5 w-3.5 shrink-0 rounded-full border-2 border-[#c4a574] bg-white"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm leading-snug text-[#16120f]/80">{{ $item->action }}</p>
                            <p class="mt-0.5 text-xs text-[#16120f]/40">{{ $item->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-[#16120f]/10 px-4 py-8 text-center text-sm text-[#16120f]/45">
                        Activity will appear as your team works.
                    </li>
                @endforelse
            </ul>
        </div>
    </section>
</div>

@script
<script>
    const initProjectsChart = () => {
        const el = document.getElementById('projectsChart');
        if (!el || !window.Chart) return;

        if (el._chart) {
            el._chart.destroy();
        }

        const labels = @json($stats['projects_chart']['labels']);
        const data = @json($stats['projects_chart']['data']);
        const colors = ['#c4a574', '#9b7b4b', '#16120f', '#6b645c', '#d4b584', '#ebe4d8'];

        el._chart = new Chart(el, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                    borderRadius: 10,
                    maxBarThickness: 36,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#16120f',
                        titleFont: { family: 'Outfit' },
                        bodyFont: { family: 'Outfit' },
                        padding: 12,
                        cornerRadius: 10,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b645c', font: { size: 11, family: 'Outfit' }, maxRotation: 0 },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#6b645c', font: { size: 11, family: 'Outfit' } },
                        grid: { color: 'rgba(22,18,15,0.06)' },
                        border: { display: false },
                    },
                },
            },
        });
    };

    initProjectsChart();
    document.addEventListener('livewire:navigated', initProjectsChart);
</script>
@endscript
