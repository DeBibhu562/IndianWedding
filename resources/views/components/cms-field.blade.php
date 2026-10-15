@props(['row', 'value' => ''])

<label {{ $attributes->class('block text-sm') }}>
    <span class="font-medium text-[#16120f]/70">{{ $row->label ?: $row->key }}</span>

    @if ($row->type === 'image')
        @php $current = $value; @endphp
        @if ($current)
            <img src="{{ $current }}" alt="" class="mt-2 h-36 w-full max-w-sm rounded-2xl object-cover ring-1 ring-black/5">
        @endif
        <input
            type="file"
            accept="image/jpeg,image/png,image/webp,image/gif"
            wire:model="imageUploads.{{ $row->key }}"
            class="mt-2 block w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm file:mr-4 file:rounded-full file:border-0 file:bg-[#16120f] file:px-4 file:py-2 file:text-sm file:text-white"
        >
        <div wire:loading wire:target="imageUploads.{{ $row->key }}" class="mt-1 text-xs text-[#16120f]/50">Uploading…</div>
        <input
            type="url"
            wire:model="fields.{{ $row->key }}"
            placeholder="Or paste image URL"
            class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm"
        >
    @elseif ($row->type === 'input')
        <input class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" wire:model="fields.{{ $row->key }}">
    @else
        <x-studio.rich-textarea
            :model="'fields.'.$row->key"
            :rows="strlen((string) $value) > 180 ? 6 : 3"
            class="mt-2"
        />
    @endif
</label>
