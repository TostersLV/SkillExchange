@props(['posts', 'filtered' => false, 'emptyTitle' => null, 'emptyText' => null, 'showEmptyAction' => true])

@if ($posts->isEmpty())
    <x-swap.empty :icon="$filtered ? 'magnifying-glass' : 'squares-plus'"
        :title="$emptyTitle ?? ($filtered ? 'No offers match your search' : 'No offers yet')">
        {{ $emptyText ?? ($filtered ? 'Try a different keyword or category.' : 'Be the first to share a skill with the community.') }}
        @if ($showEmptyAction)
        <x-slot name="actions">
            @if ($filtered)
                <x-swap.button variant="secondary" size="sm" href="{{ route('home') }}#listings">Clear filters</x-swap.button>
            @else
                <x-swap.button size="sm" href="{{ route('posts.create') }}">Share a skill</x-swap.button>
            @endif
        </x-slot>
        @endif
    </x-swap.empty>
@else
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            @php($isAvailable = $post->status === \App\PostStatus::AVAILABLE)
            <a href="{{ route('posts.show', $post) }}" wire:navigate wire:key="post-{{ $post->id }}"
                @unless ($isAvailable) aria-disabled="true" tabindex="-1" @endunless
                class="group block rounded-xl motion-safe:animate-fade-in focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand {{ $isAvailable ? '' : 'pointer-events-none opacity-80' }}"
                style="animation-delay: {{ min($loop->index, 8) * 40 }}ms">
                <x-swap.card :interactive="$isAvailable" padding="none" @class(['flex h-full flex-col', '!bg-raised !shadow-none' => ! $isAvailable])>
                    <div class="flex items-center justify-between gap-2 px-5 pt-5">
                        <x-swap.badge>{{ str($post->category->name)->limit(24) }}</x-swap.badge>
                        <x-swap.status :status="$post->status" />
                    </div>

                    <div class="flex-1 space-y-3 px-5 pt-5 pb-5">
                        <div>
                            <p class="eyebrow">Offering</p>
                            <p class="mt-1 text-lg leading-snug font-semibold break-words text-strong">{{ $post->offering_skill }}</p>
                        </div>

                        <div class="flex items-center gap-3 text-strong">
                            <span class="h-px flex-1 bg-line"></span>
                            <span class="flex size-7 items-center justify-center rounded-full border border-line-strong bg-surface">
                                <x-swap.icon class="size-3.5" />
                                <span class="sr-only">in exchange for</span>
                            </span>
                            <span class="h-px flex-1 bg-line"></span>
                        </div>

                        <div>
                            <p class="eyebrow">Looking for</p>
                            <p class="mt-1 text-lg leading-snug font-semibold break-words text-strong">{{ $post->looking_skill }}</p>
                        </div>

                        @if ($post->description)
                            <p class="line-clamp-2 pt-1 text-sm leading-6 text-muted">{{ $post->description }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 border-t border-line px-5 py-4">
                        <div class="flex min-w-0 items-center gap-2.5">
                            <flux:avatar size="xs" :name="$post->user->username" :initials="$post->user->initials()" />
                            <span class="truncate text-sm font-medium text-strong">{{ $post->user->username }}</span>
                        </div>

                        @if ($post->user->reputation !== null)
                            <x-star-rating :rating="$post->user->reputation" />
                        @else
                            <span class="text-xs text-muted">No reviews yet</span>
                        @endif
                    </div>
                </x-swap.card>
            </a>
        @endforeach
    </div>
@endif
