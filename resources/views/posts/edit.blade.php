<x-layouts::app :title="__('Edit')">
    <div class="mx-auto w-full max-w-lg p-6 lg:p-8">
        <flux:heading size="xl" level="1">{{ __('Edit post') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Update the skill you are offering and what you want in return.') }}</flux:text>

        <form method="POST" action="{{ route('posts.update', $post) }}" class="mt-8 space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle" :heading="__('Please fix the errors below.')" />
            @endif

            <flux:input name="offering_skill" :value="old('offering_skill', $post->offering_skill)" type="text" :label="__('Skill you are offering')" :placeholder="__('e.g. Guitar lessons')"/>

            <flux:input name="looking_skill" :value="old('looking_skill', $post->looking_skill)" type="text" :label="__('Skill you are looking for')" :placeholder="__('e.g. Spanish conversation')"/>

            <flux:select name="category_id" :label="__('Category')" required>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}" :selected="old('category_id', $post->category_id) == $category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea name="description" :label="__('Description')" rows="4" :placeholder="__('Describe what you are offering and what you hope to learn.')">{{ old('description', $post->description) }}</flux:textarea>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('posts.show', $post) }}" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Save changes') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
