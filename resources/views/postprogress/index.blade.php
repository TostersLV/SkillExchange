<x-layouts::app title="In Progress">
    <div class="mx-auto w-full max-w-4xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="In progress">
            Exchanges you're currently part of. Open one to chat and mark it complete.
        </x-swap.page-header>

        @if ($matches->isEmpty())
            <x-swap.empty class="mt-8" icon="arrow-path" title="No exchanges in progress">
                When one of your offers is accepted, or you accept someone else's, it will show up here.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('posts.offers') }}" size="sm">Review your offers</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <p class="mt-8 mb-3 text-sm text-muted">
                <strong class="font-bold text-strong">{{ $matches->count() }}</strong> {{ str('exchange')->plural($matches->count()) }}
            </p>

            <div class="space-y-3">
                @foreach ($matches as $match)
                    @php($otherUser = $match->user_id === auth()->id() ? $match->post->user : $match->user)
                    @php($isOpen = $match->post->status === \App\PostStatus::IN_PROGRESS)
                    <a href="{{ route('posts.progress.show', $match) }}" wire:navigate wire:key="match-{{ $match->id }}"
                        class="group grid items-center gap-5 rounded-2xl border border-line bg-surface p-5 card-shadow transition-[box-shadow,border-color] duration-200 motion-safe:animate-fade-in hover:border-line-strong hover:card-shadow-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:px-6 lg:grid-cols-[220px_minmax(0,1fr)_auto] lg:gap-6">
                        <x-swap.member :user="$otherUser" :caption="'Started '.$match->created_at->diffForHumans()" />

                        <x-swap.trade :offering="$match->post->offering_skill" :looking="$match->post->looking_skill" :active="$isOpen" />

                        <div class="flex items-center justify-between gap-3 lg:justify-end">
                            <x-swap.status :status="$match->post->status" />
                            <span class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-line px-4 text-sm font-semibold text-strong transition-colors group-hover:bg-raised">
                                <flux:icon.chat-bubble-left-right variant="micro" class="size-4" aria-hidden="true" />
                                Open
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
