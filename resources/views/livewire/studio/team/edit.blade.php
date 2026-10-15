<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9b7b4b]">Team</p>
            <h1 class="font-[Cormorant_Garamond] text-4xl">Edit team member</h1>
            <p class="mt-2 text-sm text-[#16120f]/60">Update {{ $member->user?->name }}&rsquo;s profile, role, or password.</p>
        </div>
        <a href="{{ route('app.team.index') }}" class="rounded-full bg-white px-4 py-2 text-sm ring-1 ring-black/10">Back to team</a>
    </div>

    <x-swal-flash />

    <form wire:submit="save" class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        @include('livewire.studio.team._member-form', ['roles' => $roles, 'isEdit' => true])

        <div class="mt-8 flex flex-wrap gap-2 border-t border-black/5 pt-6">
            <button type="submit" class="rounded-full bg-[#16120f] px-6 py-2.5 text-sm font-medium text-white shadow-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save changes</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
            <a href="{{ route('app.team.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm ring-1 ring-black/10">Cancel</a>
        </div>
    </form>
</div>
