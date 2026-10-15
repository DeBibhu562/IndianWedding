<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Team</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Roles</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#16120f]/60">Built-in roles cover most studios. Create custom roles for specialists (drone, assistant, sales, etc.) and pick exact permissions.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canManageCustomRoles)
                <a href="{{ route('app.team.roles.create') }}" class="rounded-full bg-[#16120f] px-4 py-2.5 text-sm font-medium text-white shadow-sm">Add new role</a>
            @endif
            @if ($canAddMember)
                <a href="{{ route('app.team.create') }}" class="rounded-full bg-white px-4 py-2.5 text-sm ring-1 ring-black/10">Add team member</a>
            @endif
            <a href="{{ route('app.team.index') }}" class="rounded-full bg-white px-4 py-2.5 text-sm ring-1 ring-black/10">All team</a>
        </div>
    </div>

    <x-swal-flash />

    @if ($canManagePermissions)
        <a href="{{ route('app.team.permissions') }}" class="flex items-center justify-between rounded-[28px] bg-[#16120f] px-6 py-4 text-white shadow-sm transition hover:bg-[#2a2218]">
            <span>
                <span class="block text-sm font-medium">Custom permissions per member</span>
                <span class="mt-0.5 block text-xs text-white/60">Override grants and revokes for individual team members.</span>
            </span>
            <x-studio.icon name="arrow" class="h-5 w-5 text-[#c4a574]" />
        </a>
    @endif

    @if ($customRoles->isNotEmpty())
        <section>
            <h2 class="text-sm font-semibold text-[#16120f]">Your custom roles</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($customRoles as $custom)
                    <article class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#c4a574]/30">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $custom->name }}</h3>
                                <p class="mt-1 text-xs text-[#16120f]/45">{{ count($custom->permissionList()) }} permissions</p>
                            </div>
                            @if ($canManageCustomRoles)
                                <div class="flex shrink-0 gap-3 text-xs">
                                    <a href="{{ route('app.team.roles.edit', $custom) }}" class="text-[#16120f]/70 hover:text-[#16120f]">Edit</a>
                                    <button type="button" wire:click="deleteRole({{ $custom->id }})" wire:confirm="Delete role &quot;{{ $custom->name }}&quot;?" class="text-rose-700">Delete</button>
                                </div>
                            @endif
                        </div>
                        @if ($custom->description)
                            <p class="mt-3 text-sm leading-relaxed text-[#16120f]/70">{{ $custom->description }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <h2 class="text-sm font-semibold text-[#16120f]">Built-in roles</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @foreach (\App\Enums\Role::cases() as $role)
                @continue($role === \App\Enums\Role::Client)
                @php
                    $canAssign = in_array($role, $assignable, true);
                @endphp
                <article class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5 {{ $canAssign ? '' : 'opacity-75' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-[Cormorant_Garamond] text-2xl">{{ $role->label() }}</h3>
                        @if ($canAssign)
                            <span class="shrink-0 rounded-full bg-[#f6f1ea] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-[#9b7b4b]">You can assign</span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-[#16120f]/70">{{ $descriptions[$role->value] ?? 'Staff role for your studio.' }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
