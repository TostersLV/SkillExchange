<x-layouts::app title="Offers">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Offers">
            Offers people have sent for your posts.
        </x-swap.page-header>

        @if ($offers->isEmpty())
            <x-swap.empty class="mt-8" icon="inbox" title="No offers yet">
                When someone offers to exchange with one of your posts, it will appear here for you to accept or decline.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('posts.create') }}" size="sm">Share a skill</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <div class="mt-8 space-y-3">
                @foreach ($offers as $offer)
                    <x-swap.card class="space-y-4 motion-safe:animate-fade-in" wire:key="offer-{{ $offer->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <flux:heading size="lg">
                                    <a href="{{ route('posts.show', $offer->post) }}"
                                        class="rounded hover:underline">{{ $offer->post->offering_skill }}</a>
                                </flux:heading>
                                <flux:text size="sm" class="mt-1">
                                    from
                                    <a href="{{ route('profile.show', $offer->user) }}"
                                        class="font-semibold text-brand underline decoration-brand/40 underline-offset-2 hover:decoration-brand">{{ $offer->user->username }}</a>
                                    &middot; {{ $offer->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <x-swap.status :status="$offer->status" />
                        </div>

                        @if ($offer->message)
                            <blockquote class="rounded-lg border border-line bg-raised px-4 py-3 text-sm leading-6 whitespace-pre-line text-fg">{{ $offer->message }}</blockquote>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            @can('accept', $offer)
                                <form method="POST" action="{{ route('post.offers.accept', $offer) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-swap.button type="submit" size="sm" variant="primary">
                                        <flux:icon.check variant="micro" />
                                        Accept
                                    </x-swap.button>
                                </form>
                            @endcan
                            @can('reject', $offer)
                                <form method="POST" action="{{ route('posts.offers.reject', $offer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-swap.button type="submit" size="sm" variant="danger">Reject</x-swap.button>
                                </form>
                            @endcan
                        </div>
                    </x-swap.card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
