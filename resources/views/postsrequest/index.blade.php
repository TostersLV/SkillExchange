<x-layouts::app title="Requests">
    <div class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Requests">
            Offers you have sent to other people.
        </x-swap.page-header>

        @if ($offers->isEmpty())
            <x-swap.empty class="mt-8" icon="paper-airplane" title="No requests sent yet">
                Find an offer you like and send a request to start an exchange.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('home') }}" size="sm">Browse offers</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <div class="mt-8 space-y-3">
                @foreach ($offers as $offer)
                    <x-swap.card class="space-y-4 motion-safe:animate-fade-in" wire:key="offer-{{ $offer->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('posts.show', $offer->post) }}" class="inline-block rounded hover:underline">
                                    <flux:heading size="lg">{{ $offer->post->offering_skill }}</flux:heading>
                                </a>
                                <flux:text size="sm" class="mt-1">
                                    To <a href="{{ route('profile.show', $offer->post->user) }}"
                                        class="font-semibold text-brand underline decoration-brand/40 underline-offset-2 hover:decoration-brand">{{ $offer->post->user->username }}</a>
                                    &middot;
                                    {{ $offer->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <x-swap.status :status="$offer->status" />
                        </div>

                        @if ($offer->message)
                            <blockquote class="rounded-lg border border-line bg-raised px-4 py-3 text-sm leading-6 whitespace-pre-line text-fg">{{ $offer->message }}</blockquote>
                        @endif

                        @can('cancel', $offer)
                            <div class="flex justify-end">
                                <form method="POST" action="{{ route('posts.offers.cancel', $offer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-swap.button type="submit" variant="ghost" size="sm">
                                        <flux:icon.x-mark variant="micro" />
                                        Cancel offer
                                    </x-swap.button>
                                </form>
                            </div>
                        @endcan
                    </x-swap.card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
