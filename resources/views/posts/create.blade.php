<x-layouts::app title="Post an exchange">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Post an exchange">
            Tell the community what you can teach and what you'd like to learn in return.
        </x-swap.page-header>

        <x-swap.card padding="lg" class="mt-8">
            <form method="POST" action="{{ route('post.store') }}" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <flux:callout variant="danger" icon="exclamation-triangle" heading="Please fix the errors below." />
                @endif

                <x-posts.exchange-fields :categories="$categories" />

                <div class="flex justify-end gap-3 border-t border-line pt-6">
                    <x-swap.button href="{{ route('home') }}" variant="ghost">Cancel</x-swap.button>
                    <x-swap.button type="submit" variant="swap">
                        <x-swap.icon class="size-4" />
                        Post exchange
                    </x-swap.button>
                </div>
            </form>
        </x-swap.card>
    </div>
</x-layouts::app>
