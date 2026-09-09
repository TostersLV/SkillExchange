<x-layouts::app :title="$post->offering_skill">
    <div class="mx-auto w-full max-w-2xl p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <flux:button href="{{ route('home') }}" variant="ghost" size="sm" icon="arrow-left">{{ __('Back to posts') }}</flux:button>

            <div class="flex items-center gap-2">
                @can('update', $post)
                    <flux:button href="{{ route('posts.edit', $post) }}" variant="ghost" size="sm" icon="pencil-square">{{ __('Edit') }}</flux:button>
                @endcan

                <livewire:posts.send-offer :post="$post" />
            </div>
        </div>

        <flux:card class="mt-6 space-y-6">
            <div class="flex items-center justify-between">
                <flux:badge size="sm" color="zinc">{{ $post->category->name }}</flux:badge>
                <flux:badge size="sm" :color="$post->status === 'available' ? 'green' : 'zinc'">{{ ucfirst($post->status) }}</flux:badge>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Offering') }}</flux:text>
                    <flux:heading size="xl">{{ $post->offering_skill }}</flux:heading>
                </div>

                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Looking for') }}</flux:text>
                    <flux:heading size="xl">{{ $post->looking_skill }}</flux:heading>
                </div>
            </div>

            @if ($post->description)
                <div>
                    <flux:text size="sm" class="text-zinc-500">{{ __('Description') }}</flux:text>
                    <flux:text class="mt-1 whitespace-pre-line">{{ $post->description }}</flux:text>
                </div>
            @endif

            <flux:separator variant="subtle" />

            <flux:text size="sm" class="text-zinc-500">{{ __('Posted by') }} {{ $post->user->username }} &middot; {{ $post->created_at->diffForHumans() }}</flux:text>
        </flux:card>
    </div>
</x-layouts::app>
