<x-layouts::app title="Edit">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Edit your exchange">
            Update the skill you are offering and what you want in return.
        </x-swap.page-header>

        <x-swap.card padding="lg" class="mt-8">
            <form method="POST" action="{{ route('posts.update', $post) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <flux:callout variant="danger" icon="exclamation-triangle" heading="Please fix the errors below." />
                @endif

                <x-posts.exchange-fields :categories="$categories" :post="$post" />

                <div class="flex justify-end gap-3 border-t border-line pt-6">
                    <x-swap.button href="{{ route('posts.show', $post) }}" variant="ghost">Cancel</x-swap.button>
                    <x-swap.button type="submit" variant="swap">Save changes</x-swap.button>
                </div>
            </form>
        </x-swap.card>
    </div>
</x-layouts::app>
