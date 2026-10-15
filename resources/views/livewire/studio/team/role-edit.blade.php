<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Roles</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Edit custom role</h1>
            <p class="mt-2 text-sm text-[#16120f]/60">Update the name, description, or permissions. Changes apply to everyone assigned this role.</p>
        </div>
        <a href="{{ route('app.team.roles') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to roles</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
            <div class="grid gap-4">
                <label class="block text-sm">
                    <span class="font-medium text-[#16120f]/70">Role name <span class="text-rose-600">*</span></span>
                    <input wire:model="name" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="e.g. Drone operator">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="block text-sm">
                    <span class="font-medium text-[#16120f]/70">Description</span>
                    <textarea wire:model="description" rows="2" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="Short summary for your team"></textarea>
                </label>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs text-[#16120f]/50">Start from template:</span>
                <button type="button" wire:click="applyTemplate('photographer')" class="rounded-full bg-[#f6f1ea] px-3 py-1 text-xs">Photographer</button>
                <button type="button" wire:click="applyTemplate('manager')" class="rounded-full bg-[#f6f1ea] px-3 py-1 text-xs">Manager</button>
                <button type="button" wire:click="applyTemplate('editor')" class="rounded-full bg-[#f6f1ea] px-3 py-1 text-xs">Editor</button>
            </div>
        </section>

        <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
            <h2 class="text-sm font-semibold text-[#16120f]">Permissions</h2>
            <div class="mt-6 space-y-6">
                @foreach ($groups as $group => $permissions)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#9b7b4b]">{{ $group }}</p>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-[#f6f1ea] px-3 py-2 text-sm">
                                    <input type="checkbox" wire:model="permissionStates.{{ $permission->value }}" class="rounded border-[#16120f]/20 text-[#16120f]">
                                    {{ $permission->label() }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white shadow-sm">Save changes</button>
    </form>
</div>
