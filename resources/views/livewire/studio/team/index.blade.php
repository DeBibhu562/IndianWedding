<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-[Cormorant_Garamond] text-4xl">Team</h1>
        <div class="flex flex-wrap gap-2">
            @if ($canManagePermissions)
            <a href="{{ route('app.team.permissions') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-medium shadow-sm ring-1 ring-black/5 hover:bg-[#f6f1ea]">
                Manage permissions
            </a>
            @endif
            @if ($canCreate)
            <a href="{{ route('app.team.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#2a2218]">
                Add member
            </a>
            @endif
        </div>
    </div>

    <x-swal-flash />

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($members as $member)
        <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-lg font-medium">{{ $member->user?->name }}</p>
                    <p class="text-sm text-[#16120f]/60">{{ $member->roleLabel() }}</p>
                    <p class="mt-1 text-xs text-[#16120f]/45">{{ $member->user?->email }}</p>
                </div>
                <div class="flex gap-2">
                    @if ($canEdit && ! $member->is_owner && auth()->user()?->can('update', $member))
                    <a href="{{ route('app.team.edit', $member) }}" class="text-xs text-[#9b7b4b]">Edit</a>
                    @endif
                    @if ($canDelete && ! $member->is_owner && $member->user_id !== auth()->id())
                    <button type="button" wire:click="delete({{ $member->id }})" wire:confirm="Remove this team member?" class="text-xs text-rose-700">Delete</button>
                    @endif
                </div>
            </div>
            <p class="mt-2 text-sm text-[#16120f]/50">{{ $workload[$member->user_id] ?? 0 }} active assignments</p>
            @if ($member->roleEnum() === \App\Enums\Role::StudioAdmin)
            <p class="mt-3 inline-flex rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold text-[#9b7b4b]">Full studio access</p>
            @elseif ($member->is_owner)
            <p class="mt-3 inline-flex rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold text-[#9b7b4b]">Owner</p>
            @endif
        </article>
        @empty
        <p class="md:col-span-2">No team members yet.</p>
        @endforelse
    </div>
</div>
