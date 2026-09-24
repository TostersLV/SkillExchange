<x-layouts::app :title="$post->offering_skill">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-swap.button href="{{ route('home') }}" variant="ghost" size="sm" class="-ms-3">
                <flux:icon.arrow-left variant="micro" />
                Back to offers
            </x-swap.button>

            <div class="flex flex-wrap items-center gap-2">
                @can('update', $post)
                    <x-swap.button href="{{ route('posts.edit', $post) }}" variant="secondary" size="sm">
                        <flux:icon.pencil-square variant="micro" />
                        Edit
                    </x-swap.button>
                @endcan

                @can('delete', $post)
                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                        onsubmit="return confirm('Delete this post? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <x-swap.button type="submit" variant="danger" size="sm">
                            <flux:icon.trash variant="micro" />
                            Delete
                        </x-swap.button>
                    </form>
                @endcan

                <livewire:posts.send-offer :post="$post" />
            </div>
        </div>

        <x-swap.card padding="none" class="mt-6">
            <div class="flex items-center justify-between gap-2 px-6 pt-6 sm:px-8 sm:pt-8">
                <x-swap.badge>{{ $post->category->name }}</x-swap.badge>
                <x-swap.status :status="$post->status" />
            </div>

            <div class="grid items-center gap-4 px-6 pt-6 sm:grid-cols-[1fr_auto_1fr] sm:px-8">
                <div class="min-w-0 rounded-lg border border-line bg-raised p-5">
                    <p class="eyebrow">Offering</p>
                    <h1 class="mt-1.5 text-2xl leading-tight font-semibold tracking-tight break-words text-strong">
                        {{ $post->offering_skill }}</h1>
                </div>

                <span
                    class="mx-auto flex size-9 items-center justify-center rounded-full border border-line-strong bg-surface text-strong max-sm:rotate-90">
                    <x-swap.icon class="size-4" />
                    <span class="sr-only">in exchange for</span>
                </span>

                <div class="min-w-0 rounded-lg border border-line bg-raised p-5">
                    <p class="eyebrow">Looking for</p>
                    <p class="mt-1.5 text-2xl leading-tight font-semibold tracking-tight break-words text-strong">
                        {{ $post->looking_skill }}</p>
                </div>
            </div>

            @if ($post->description)
                <div class="px-6 pt-8 sm:px-8">
                    <p class="eyebrow">Description</p>
                    <p class="mt-2 leading-7 whitespace-pre-line text-fg">{{ $post->description }}</p>
                </div>
            @endif

            <a href="{{ route('profile.show', $post->user) }}" wire:navigate
                class="group mt-8 flex items-center justify-between gap-3 rounded-b-xl border-t border-line px-6 py-5 transition-colors duration-150 hover:bg-raised sm:px-8">
                <div class="flex items-center gap-3">
                    <flux:avatar size="sm" :name="$post->user->username" :initials="$post->user->initials()" />

                    <div>
                        <p class="text-sm font-semibold text-strong group-hover:underline">{{ $post->user->username }}</p>
                        <p class="text-sm text-muted">Posted {{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                @if ($post->user->reputation !== null)
                    <x-star-rating :rating="$post->user->reputation" />
                @else
                    <span class="text-sm text-muted">No reviews yet</span>
                @endif
            </a>
        </x-swap.card>
    </div>
</x-layouts::app>
