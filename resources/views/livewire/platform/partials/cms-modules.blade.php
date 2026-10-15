@if ($activeModule === 'social')
    <form wire:submit="saveSocial" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            @foreach (\App\Support\SocialPlatforms::definitions() as $def)
                <label class="block rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <span class="flex items-center gap-2 text-sm font-medium">
                        @include('components.partials.social-icon', ['platform' => $def['id'], 'class' => 'h-4 w-4 opacity-70'])
                        {{ $def['label'] }}
                    </span>
                    <input type="url" class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm" placeholder="{{ $def['placeholder'] }}" wire:model="fields.{{ $def['key'] }}">
                    @error('fields.'.$def['key']) <p class="mt-2 text-xs text-rose-700">{{ $message }}</p> @enderror
                </label>
            @endforeach
        </div>
        <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save social links</button>
    </form>
@endif

@if ($activeModule === 'testimonials')
    <form wire:submit="addTestimonial" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
        <input wire:model="testimonial_author" placeholder="Author" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="testimonial_role" placeholder="Role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <x-studio.rich-textarea model="testimonial_quote" placeholder="Quote" :rows="4" />
        <button class="rounded-full bg-[#16120f] py-3 text-white">Add testimonial</button>
    </form>
    <div class="space-y-3">
        @foreach ($testimonials as $item)
            <article class="rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                <p class="font-[Cormorant_Garamond] text-xl">“{{ $item->quote }}”</p>
                <p class="mt-2 text-sm opacity-60">{{ $item->author }} · {{ $item->role }}</p>
                <button type="button" wire:click="deleteTestimonial({{ $item->id }})" wire:confirm="Remove this testimonial?" class="mt-2 text-sm text-rose-700">Remove</button>
            </article>
        @endforeach
    </div>
@endif

@if ($activeModule === 'portfolio')
    <form wire:submit="addPortfolio" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
        <input wire:model="portfolio_title" placeholder="Title" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input type="file" wire:model="portfolio_uploads" accept="image/*" multiple class="block w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm file:mr-4 file:rounded-full file:border-0 file:bg-[#16120f] file:px-4 file:py-2 file:text-sm file:text-white">
        <input wire:model="portfolio_image" placeholder="Image URL (optional if uploading)" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <input wire:model="portfolio_category" placeholder="Category" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <button class="rounded-full bg-[#16120f] py-3 text-white">Add gallery image</button>
    </form>
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ($portfolio as $item)
            <figure class="relative rounded-[28px] bg-white p-2 shadow-sm ring-1 ring-black/5">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-40 w-full rounded-2xl object-cover">
                <button type="button" wire:click="deletePortfolio({{ $item->id }})" wire:confirm="Remove this image?" class="absolute right-4 top-4 rounded-full bg-white px-2 py-0.5 text-xs shadow">Remove</button>
                <figcaption class="mt-2 px-1 text-xs">{{ $item->title }}</figcaption>
            </figure>
        @endforeach
    </div>
@endif

@if (in_array($activeModule, ['pricing_wedding', 'pricing_addons'], true))
    @include('livewire.platform.partials.cms-pricing-packages')
@endif

@if ($activeModule === 'faq')
    <form wire:submit="addFaq" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
        <input wire:model="faq_question" placeholder="Question" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
        <x-studio.rich-textarea model="faq_answer" placeholder="Answer" :rows="5" />
        <button class="rounded-full bg-[#16120f] py-3 text-white">Add FAQ</button>
    </form>
    <div class="space-y-3">
        @foreach ($faqs as $faq)
            <details class="rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                <p class="mt-2 text-sm opacity-70">{{ $faq->answer }}</p>
                <button type="button" wire:click="deleteFaq({{ $faq->id }})" wire:confirm="Remove this FAQ?" class="mt-2 text-sm text-rose-700">Remove</button>
            </details>
        @endforeach
    </div>
@endif
