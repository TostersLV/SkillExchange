<x-layouts::app title="Create">
    <div class="mx-auto w-full max-w-2xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Share a skill">
            Tell the community what you can teach and what you'd like to learn in return.
        </x-swap.page-header>

        <x-swap.card padding="lg" class="mt-8">
            <form method="POST" action="/create" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <flux:callout variant="danger" icon="exclamation-triangle" heading="Please fix the errors below." />
                @endif

                <flux:input name="offering_skill" :value="old('offering_skill')" type="text" label="Skill you are offering"
                    description="Be specific, e.g. beginner guitar lessons or CV review."
                    placeholder="e.g. Guitar lessons" />

                <flux:input name="looking_skill" :value="old('looking_skill')" type="text" label="Skill you are looking for"
                    description="What would you like to learn in return?"
                    placeholder="e.g. Spanish conversation" />

                <x-swap.select name="category_id" label="Category" placeholder="Choose a category..." required>
                    @foreach ($categories as $category)
                        <flux:select.option value="{{ $category->id }}" :selected="old('category_id') == $category->id">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </x-swap.select>

                <flux:textarea name="description" label="Description" rows="4"
                    description="Optional. Your experience level, availability, or how you'd like to meet."
                    placeholder="Describe what you are offering and what you hope to learn.">{{ old('description') }}</flux:textarea>

                <div class="flex justify-end gap-3 border-t border-line pt-6">
                    <x-swap.button href="{{ route('home') }}" variant="ghost">Cancel</x-swap.button>
                    <x-swap.button type="submit">Publish offer</x-swap.button>
                </div>
            </form>
        </x-swap.card>
    </div>
</x-layouts::app>
