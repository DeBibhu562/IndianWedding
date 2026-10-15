@props(['roleOptions', 'isEdit' => false])

<div class="space-y-8">
    <section>
        <h2 class="text-sm font-semibold text-[#16120f]">Profile</h2>
        <p class="mt-1 text-xs text-[#16120f]/55">Basic contact details shown across the studio app.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <label class="block text-sm sm:col-span-2">
                <span class="font-medium text-[#16120f]/70">Full name <span class="text-rose-600">*</span></span>
                <input wire:model="name" autocomplete="name" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="e.g. Rahul Sharma">
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </label>
            <label class="block text-sm sm:col-span-2">
                <span class="font-medium text-[#16120f]/70">Work email <span class="text-rose-600">*</span></span>
                <input
                    wire:model="email"
                    type="email"
                    autocomplete="email"
                    @disabled($isEdit)
                    class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm disabled:cursor-not-allowed disabled:opacity-60"
                    placeholder="name@unikstudio.in"
                >
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                @if ($isEdit)
                    <p class="mt-1 text-xs text-[#16120f]/45">Email is fixed after the account is created. Contact support to change it.</p>
                @endif
            </label>
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">Phone</span>
                <input wire:model="phone" type="tel" autocomplete="tel" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="+91 98183 61412">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </label>
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">WhatsApp</span>
                <input wire:model="whatsapp" type="tel" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="+91 98183 61412">
                @error('whatsapp') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </label>
        </div>
    </section>

    <section x-data="{ show: false, showConfirm: false }">
        <h2 class="text-sm font-semibold text-[#16120f]">Studio login</h2>
        <p class="mt-1 text-xs text-[#16120f]/55">
            @if ($isEdit)
                Leave blank to keep the current password. Set a new one only if they forgot it or you are onboarding them again.
            @else
                Set the password they will use at <span class="font-medium">Studio admin login</span>. Share it securely after saving.
            @endif
        </p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">
                    Password @if (! $isEdit)<span class="text-rose-600">*</span>@endif
                </span>
                <div class="relative mt-2">
                    <input
                        wire:model="password"
                        :type="show ? 'text' : 'password'"
                        autocomplete="{{ $isEdit ? 'new-password' : 'new-password' }}"
                        class="w-full rounded-2xl bg-[#f6f1ea] py-3 pl-4 pr-11 text-sm"
                        placeholder="{{ $isEdit ? 'New password (optional)' : 'Min. 10 characters' }}"
                    >
                    <button type="button" class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-[#16120f]/45 hover:text-[#16120f]" @click="show = !show" tabindex="-1" aria-label="Toggle password visibility">
                        <svg x-show="!show" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="show" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </label>
            <label class="block text-sm">
                <span class="font-medium text-[#16120f]/70">
                    Confirm password @if (! $isEdit)<span class="text-rose-600">*</span>@endif
                </span>
                <div class="relative mt-2">
                    <input
                        wire:model="password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        autocomplete="new-password"
                        class="w-full rounded-2xl bg-[#f6f1ea] py-3 pl-4 pr-11 text-sm"
                        placeholder="Repeat password"
                    >
                    <button type="button" class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-[#16120f]/45 hover:text-[#16120f]" @click="showConfirm = !showConfirm" tabindex="-1" aria-label="Toggle confirm password visibility">
                        <svg x-show="!showConfirm" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="showConfirm" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </label>
        </div>
    </section>

    <section>
        <h2 class="text-sm font-semibold text-[#16120f]">Role & access</h2>
        <p class="mt-1 text-xs text-[#16120f]/55">Controls what they can see and do in the studio dashboard.</p>
        <label class="mt-4 block text-sm">
            <span class="font-medium text-[#16120f]/70">Role <span class="text-rose-600">*</span></span>
            <select wire:model.live="role" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm">
                @foreach ($roleOptions as $roleOption)
                    <option value="{{ $roleOption['value'] }}">
                        {{ $roleOption['label'] }}@if ($roleOption['custom'] ?? false) (custom)@endif
                    </option>
                @endforeach
            </select>
            @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            @php
                $selected = collect($roleOptions)->firstWhere('value', $role ?? '');
                $roleHint = $selected['description'] ?? match ($role ?? '') {
                    'studio_admin' => 'Full studio control, including permanent delete. Only assign to trusted owners.',
                    'admin' => 'Full access to leads, projects, finance, and team management. Cannot permanently delete records.',
                    'manager' => 'Runs pipeline, clients, calendar, and team assignments day to day.',
                    'photographer' => 'Sees assigned shoots, uploads files, and manages their calendar.',
                    'videographer' => 'Same as photographer, focused on film deliverables.',
                    'editor' => 'Post-production: raw files, uploads, and assigned edit tasks.',
                    default => 'Pick the role that matches how this person works with your studio.',
                };
            @endphp
            <p class="mt-2 rounded-2xl bg-[#f6f1ea] px-4 py-3 text-xs leading-relaxed text-[#16120f]/70">{{ $roleHint }}</p>
        </label>
    </section>

    <section>
        <h2 class="text-sm font-semibold text-[#16120f]">Account status</h2>
        <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input type="checkbox" wire:model.boolean="is_active" class="mt-1 rounded border-[#16120f]/20 text-[#16120f] focus:ring-[#c4a574]">
            <span class="text-sm">
                <span class="font-medium text-[#16120f]">Active team member</span>
                <span class="mt-0.5 block text-xs text-[#16120f]/55">Inactive users cannot sign in to the studio app.</span>
            </span>
        </label>
        @error('is_active') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </section>
</div>
