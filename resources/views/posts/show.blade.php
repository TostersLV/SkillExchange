<x-layouts::app :title="$post->offering_skill">
    @php($isAvailable = $post->status === \App\PostStatus::AVAILABLE)

    <div class="mx-auto w-full max-w-4xl px-4 py-10 sm:px-6 lg:py-14">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-swap.button href="{{ route('home') }}" variant="ghost" size="sm" class="-ms-3">
                <flux:icon.arrow-left variant="micro" />
                Back to exchanges
            </x-swap.button>

            <div class="flex flex-wrap items-center gap-2">
                @can('update', $post)
                    <x-swap.button href="{{ route('posts.edit', $post) }}" variant="secondary" size="sm">
                        <flux:icon.pencil-square variant="micro" />
                        Edit
                    </x-swap.button>
                @endcan

                @can('close', $post)
                    <form method="POST" action="{{ route('posts.close', $post) }}"
                        onsubmit="return confirm('Close this post? It stops taking offers and waiting offers are declined. Past exchanges and chats are kept.')">
                        @csrf
                        @method('PATCH')
                        <x-swap.button type="submit" variant="danger" size="sm">
                            <flux:icon.lock-closed variant="micro" />
                            Close post
                        </x-swap.button>
                    </form>
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
            </div>
        </div>

        <x-swap.card padding="none" class="mt-6 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 px-6 pt-6 sm:px-8 sm:pt-8">
                <x-swap.badge>
                    <x-swap.category-icon :name="$post->category->name" class="opacity-80" />
                    {{ $post->category->name }}
                </x-swap.badge>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-muted">Posted {{ $post->created_at->diffForHumans() }}</span>
                    <x-swap.status :status="$post->status" />
                </div>
            </div>

            <x-swap.trade :offering="$post->offering_skill" :looking="$post->looking_skill" :active="$isAvailable"
                size="lg" :heading-level="1" class="px-6 pt-6 sm:px-8" />

            @if ($post->description)
                <div class="px-6 pt-8 sm:px-8">
                    <p class="eyebrow">About this exchange</p>
                    <p class="mt-2 leading-7 whitespace-pre-line text-fg">{{ $post->description }}</p>
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-4 border-t border-line bg-page/60 px-6 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <x-swap.member :user="$post->user" link />

                <livewire:posts.send-offer :post="$post" />
            </div>
        </x-swap.card>

        <ul class="mt-6 grid gap-3 text-sm text-muted sm:grid-cols-3">
            @foreach ([
                ['icon' => 'chat-bubble-left-right', 'text' => 'Once accepted, you plan the swap in a private chat.'],
                ['icon' => 'clock', 'text' => 'Time for time: no money ever changes hands.'],
                ['icon' => 'star', 'text' => 'You both leave a review when the swap is done.'],
            ] as $tip)
                <li class="flex items-start gap-2.5 rounded-2xl border border-line bg-surface p-4">
                    <flux:icon :name="$tip['icon']" variant="outline" class="mt-0.5 size-4 shrink-0 text-swap-text" aria-hidden="true" />
                    {{ $tip['text'] }}
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts::app>
