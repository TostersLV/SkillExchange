@props(['categories', 'post' => null])

{{-- The shared fields of the create and edit forms: the two skills side by side, like a currency converter. --}}
<div class="grid items-start gap-3 rounded-[1.25rem] border border-line bg-page p-3 sm:p-4 md:grid-cols-[1fr_auto_1fr]">
    <div class="rounded-2xl bg-surface p-4">
        <flux:input name="offering_skill" :value="old('offering_skill', $post?->offering_skill)" type="text"
            label="You offer" description="Be specific, e.g. beginner guitar lessons or CV review."
            placeholder="e.g. Guitar lessons 2h" />
    </div>

    <span class="mx-auto flex size-11 items-center justify-center rounded-full bg-swap text-white shadow-md shadow-swap/30 md:mt-12 max-md:rotate-90" aria-hidden="true">
        <x-swap.icon class="size-5" />
    </span>

    <div class="rounded-2xl bg-surface p-4">
        <flux:input name="looking_skill" :value="old('looking_skill', $post?->looking_skill)" type="text"
            label="You want in return" description="What would you like to learn?"
            placeholder="e.g. Spanish conversation 2h" />
    </div>
</div>

<x-swap.select name="category_id" label="Category" placeholder="Choose a category..." required>
    @foreach ($categories as $category)
        <flux:select.option value="{{ $category->id }}" :selected="old('category_id', $post?->category_id) == $category->id">
            {{ $category->name }}
        </flux:select.option>
    @endforeach
</x-swap.select>

<flux:textarea name="description" label="Description" rows="4"
    description="Optional. Your experience level, availability, or how you'd like to meet."
    placeholder="Describe what you are offering and what you hope to learn.">{{ old('description', $post?->description) }}</flux:textarea>
