<x-layouts::app title="In Progress">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="In progress">
            Exchanges you're currently part of.
        </x-swap.page-header>

        @if ($matches->isEmpty())
            <x-swap.empty class="mt-8" icon="arrow-path" title="No exchanges in progress">
                When one of your offers is accepted, or you accept someone else's, it will show up here.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('posts.offers') }}" size="sm">Review your offers</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <div class="mt-8 space-y-3">
                @foreach ($matches as $match)
                    @php($isSender = $match->user_id === auth()->id())
                    @php($otherUsername = $isSender ? $match->post->user->username : $match->user->username)
                    <a href="{{ route('posts.progress.show', $match) }}" wire:navigate wire:key="match-{{ $match->id }}"
                        class="group block rounded-xl motion-safe:animate-fade-in focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand">
                        <x-swap.card interactive class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <span
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-line bg-raised text-strong">
                                <x-swap.icon class="size-5" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="font-semibold break-words text-strong">
                                    {{ $match->post->offering_skill }}
                                    <span class="font-normal text-muted">for</span>
                                    {{ $match->post->looking_skill }}
                                </p>
                                <p class="mt-1 text-sm text-muted">
                                    With <span class="font-medium text-fg">{{ $otherUsername }}</span> &middot;
                                    started {{ $match->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3 self-start sm:self-center">
                                <x-swap.status :status="$match->post->status" />
                                <flux:icon.chevron-right variant="micro" class="size-4 text-muted max-sm:hidden" aria-hidden="true" />
                            </div>
                        </x-swap.card>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
