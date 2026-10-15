@props([
    'model',
    'rows' => 4,
    'placeholder' => null,
    'plain' => false,
])

@if ($plain)
    <textarea
        wire:model="{{ $model }}"
        rows="{{ $rows }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->class('w-full rounded-2xl bg-[#f6f1ea] px-4 py-3') }}
    ></textarea>
@else
    <div {{ $attributes->class('rich-text-host') }} wire:ignore wire:key="rich-text-{{ $model }}">
        <div
            x-data="richTextEditor(@entangle($model).live)"
            x-init="init()"
            class="w-full"
        >
            <div x-ref="toolbar" class="rich-text-toolbar">
                <span class="ql-formats">
                    <button type="button" class="ql-bold" aria-label="Bold"></button>
                    <button type="button" class="ql-italic" aria-label="Italic"></button>
                    <button type="button" class="ql-underline" aria-label="Underline"></button>
                </span>
                <span class="ql-formats">
                    <button type="button" class="ql-list" value="ordered" aria-label="Ordered list"></button>
                    <button type="button" class="ql-list" value="bullet" aria-label="Bullet list"></button>
                </span>
                <span class="ql-formats">
                    <select class="ql-header" aria-label="Heading">
                        <option selected></option>
                        <option value="2"></option>
                        <option value="3"></option>
                    </select>
                </span>
                <span class="ql-formats">
                    <button type="button" class="ql-link" aria-label="Link"></button>
                    <button type="button" class="ql-clean" aria-label="Clear formatting"></button>
                </span>
            </div>
            <div
                x-ref="editor"
                class="rich-text-editor"
                style="min-height: {{ max(3, (int) $rows) * 1.75 }}rem"
                @if ($placeholder) data-placeholder="{{ $placeholder }}" @endif
            ></div>
        </div>
    </div>
@endif
