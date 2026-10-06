@props(['posts', 'matches' => collect(), 'filtered' => false, 'emptyTitle' => null, 'emptyText' => null, 'showEmptyAction' => true])

@if ($posts->isEmpty())
    <x-swap.empty :icon="$filtered ? 'magnifying-glass' : 'squares-plus'"
        :title="$emptyTitle ?? ($filtered ? 'No exchanges match your search' : 'No exchanges yet')">
        {{ $emptyText ?? ($filtered ? 'Try a different skill or category.' : 'Be the first to post an exchange.') }}
        @if ($showEmptyAction)
        <x-slot name="actions">
            @if ($filtered)
                <x-swap.button variant="secondary" size="sm" href="{{ route('home') }}#explore">Clear filters</x-swap.button>
            @else
                <x-swap.button variant="swap" size="sm" href="{{ route('posts.create') }}">Post an exchange</x-swap.button>
            @endif
        </x-slot>
        @endif
    </x-swap.empty>
@else
    {{-- Order-book style rows: who is trading, what for what, and how to respond. --}}
    <div class="space-y-2.5">
        <div class="grid grid-cols-[230px_minmax(0,1fr)_auto] gap-6 px-6 text-[11px] font-extrabold tracking-[0.1em] text-muted uppercase max-lg:hidden" aria-hidden="true">
            <span>Member</span>
            <span>Trade</span>
            <span class="text-right">Details</span>
        </div>

        @foreach ($posts as $post)
            @php
                $isAvailable = $post->status === \App\PostStatus::AVAILABLE;
                $isMatch = $matches->contains($post->id);
                $isOwn = $post->user_id === auth()->id();
            @endphp
            <a href="{{ route('posts.show', $post) }}" wire:navigate wire:key="post-{{ $post->id }}"
                @unless ($isAvailable) aria-disabled="true" tabindex="-1" @endunless
                style="animation-delay: {{ min($loop->index, 8) * 40 }}ms"
                @class([
                    'group grid items-center gap-5 rounded-2xl border bg-surface p-5 card-shadow motion-safe:animate-fade-in sm:px-6 lg:grid-cols-[230px_minmax(0,1fr)_auto] lg:gap-6',
                    'transition-[box-shadow,border-color] duration-200 hover:border-line-strong hover:card-shadow-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand' => $isAvailable,
                    'border-line' => ! $isMatch,
                    'border-swap/40 border-l-4 border-l-swap bg-linear-to-r from-swap-soft to-surface to-40% sm:pl-[21px]' => $isMatch,
                    'pointer-events-none opacity-60 !shadow-none' => ! $isAvailable,
                ])>
                {{-- Member --}}
                <x-swap.member :user="$post->user" />

                {{-- Trade --}}
                <div class="min-w-0 space-y-2">
                    @if ($isMatch)
                        <p class="flex items-center gap-1.5 text-xs font-bold text-swap-text">
                            <flux:icon.star variant="micro" class="size-3.5 text-swap" aria-hidden="true" />
                            Great match for you
                        </p>
                    @endif
                    <x-swap.trade :offering="$post->offering_skill" :looking="$post->looking_skill" :active="$isAvailable" />
                </div>

                {{-- Details --}}
                <div class="flex items-center justify-between gap-4 lg:justify-end">
                    <div class="flex flex-col gap-1.5 lg:items-end">
                        <div class="flex flex-wrap gap-1.5 lg:justify-end">
                            <x-swap.badge>
                                <x-swap.category-icon :name="$post->category->name" class="opacity-80" />
                                {{ str($post->category->name)->limit(24) }}
                            </x-swap.badge>
                            <x-swap.status :status="$post->status" />
                        </div>
                        <p class="text-xs text-muted">Posted {{ $post->created_at->diffForHumans() }}</p>
                    </div>

                    @if (! $isAvailable)
                        {{-- Closed exchanges cannot be opened, so they get no call to action. --}}
                    @elseif ($isOwn)
                        <span class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-semibold text-strong transition-colors group-hover:bg-raised">Your post</span>
                    @else
                        <span class="inline-flex h-10 shrink-0 items-center rounded-xl bg-swap-strong px-4 text-sm font-bold text-white transition-colors group-hover:bg-[#065f46]">Propose swap</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@endif
