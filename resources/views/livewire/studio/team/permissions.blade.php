<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-[#16120f]/50">Studio admin</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl md:text-5xl">Team permissions</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#16120f]/55">Studio admins have full access to everything. Use this page to grant or revoke permissions for other team members.</p>
        </div>
        <a href="{{ route('app.team.index') }}" class="rounded-xl bg-white px-4 py-2 text-sm shadow-sm ring-1 ring-black/5 hover:bg-[#f6f1ea]">Back to team</a>
    </div>

    <x-swal-flash />
<div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-black/5">
            <p class="px-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">Team members</p>
            <div class="mt-3 space-y-1">
                @foreach ($members as $member)
                <button
                    type="button"
                    wire:click="selectMember({{ $member->id }})"
                    class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left transition {{ $selectedMemberId === $member->id ? 'bg-[#16120f] text-white' : 'hover:bg-[#f6f1ea]' }}"
                >
                    <span>
                        <span class="block text-sm font-medium">{{ $member->user?->name }}</span>
                        <span class="block text-xs {{ $selectedMemberId === $member->id ? 'text-white/60' : 'text-[#16120f]/45' }}">{{ $member->roleLabel() }}</span>
                    </span>
                    @if ($member->roleEnum() === \App\Enums\Role::StudioAdmin)
                    <span class="rounded-full bg-[#c4a574]/20 px-2 py-0.5 text-[10px] font-semibold text-[#c4a574]">Full + delete</span>
                    @elseif ($member->roleEnum() === \App\Enums\Role::Admin)
                    <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-white/70">Full (no delete)</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2">
            @if (! $selected)
            <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                <p class="text-sm text-[#16120f]/45">Select a team member to manage their permissions.</p>
            </div>
            @elseif (in_array($selected->roleEnum(), [\App\Enums\Role::StudioAdmin, \App\Enums\Role::Admin], true))
            <div class="rounded-3xl bg-[#16120f] p-8 text-[#f6f1ea] shadow-sm">
                <h2 class="font-[Cormorant_Garamond] text-3xl">{{ $selected->user?->name }}</h2>
                @if ($selected->roleEnum() === \App\Enums\Role::StudioAdmin)
                <p class="mt-2 text-sm text-white/60">Studio Admin has full access including permanent delete across the studio.</p>
                @else
                <p class="mt-2 text-sm text-white/60">Admin can create, view and update everything, but cannot delete records. Only Studio Admin can delete.</p>
                @endif
            </div>
            @else
            <form wire:submit="savePermissions" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-[Cormorant_Garamond] text-3xl">{{ $selected->user?->name }}</h2>
                        <p class="mt-1 text-sm text-[#16120f]/50">Role: {{ $selected->roleLabel() }} · toggle permissions below</p>
                    </div>
                    <button type="submit" class="rounded-xl bg-[#16120f] px-4 py-2 text-sm font-medium text-white hover:bg-[#2a2218]">
                        Save permissions
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    @foreach ($groups as $group => $permissions)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#16120f]/45">{{ $group }}</p>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                            <label class="flex items-center gap-3 rounded-2xl bg-[#f6f1ea] px-3 py-3 text-sm">
                                <input
                                    type="checkbox"
                                    wire:model="permissionStates.{{ $permission->value }}"
                                    class="rounded border-[#c4a574] text-[#16120f] focus:ring-[#c4a574]"
                                >
                                <span>{{ $permission->label() }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
