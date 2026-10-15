<div class="mx-auto max-w-5xl space-y-8">
    <div>
        <a href="{{ route('client.dashboard') }}" class="text-sm text-white/50">← Home</a>
        <p class="mt-4 text-xs uppercase tracking-[0.25em] text-[#c4a574]">{{ $project->project_number }}</p>
        <h1 class="mt-2 font-[Cormorant_Garamond] text-5xl">{{ $project->title }}</h1>
        <p class="mt-3 text-white/70">{{ $project->venue }}{{ $project->city ? ' · '.$project->city : '' }} · {{ optional($project->wedding_date)?->toFormattedDateString() }}</p>
        <p class="mt-2 text-sm text-white/50">{{ $project->status->label() }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl bg-white/5 p-5">
            <p class="text-xs uppercase tracking-[0.2em] text-white/40">Package</p>
            <p class="mt-2 text-xl">{{ $project->package?->name ?? 'Custom' }}</p>
        </div>
        <div class="rounded-3xl bg-white/5 p-5">
            <p class="text-xs uppercase tracking-[0.2em] text-white/40">Paid</p>
            <p class="mt-2 text-xl">{{ \App\Support\Money::format($project->paidAmount()) }}</p>
        </div>
        <div class="rounded-3xl bg-white/5 p-5">
            <p class="text-xs uppercase tracking-[0.2em] text-white/40">Balance</p>
            <p class="mt-2 text-xl">{{ \App\Support\Money::format($project->balance()) }}</p>
        </div>
    </div>

    <section class="rounded-3xl bg-white/5 p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] text-white/40">Events</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse ($project->events as $event)
                <li>{{ $event->title }} · {{ optional($event->date)?->toFormattedDateString() }} {{ $event->start_time }}</li>
            @empty
                <li class="text-white/40">No events scheduled yet.</li>
            @endforelse
        </ul>
    </section>

    <section class="rounded-3xl bg-white/5 p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] text-white/40">Payments</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @foreach ($project->paymentMilestones as $m)
                <li class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                    <span>{{ $m->name }} · {{ $m->percentage }}%</span>
                    <span>{{ \App\Support\Money::format($m->amount) }} · {{ $m->status->value }}</span>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('client.payments') }}" class="mt-4 inline-block text-sm text-[#c4a574]">Open payments →</a>
    </section>

    <section class="rounded-3xl bg-white/5 p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] text-white/40">Team</h2>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($project->team as $member)
                <li>{{ $member->user?->name }} · {{ $member->roleLabel() }}</li>
            @empty
                <li class="text-white/40">Your crew will appear here once assigned.</li>
            @endforelse
        </ul>
    </section>

    @if ($project->gallery)
        <a href="{{ route('client.gallery') }}" class="block rounded-3xl bg-white/5 p-6">Gallery · {{ $project->gallery->is_released ? 'Released' : 'In progress' }}</a>
    @endif
</div>
