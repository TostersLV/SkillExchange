<x-layouts::app title="Requests">
    <div class="mx-auto w-full max-w-4xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Requests">
            Swaps you have proposed to other members.
        </x-swap.page-header>

        @if ($offers->isEmpty())
            <x-swap.empty class="mt-8" icon="paper-airplane" title="No requests sent yet">
                Find an exchange you like and propose a swap to get started.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('home') }}" variant="swap" size="sm">Explore exchanges</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <p class="mt-8 mb-3 text-sm text-muted">
                <strong class="font-bold text-strong">{{ $offers->count() }}</strong> {{ str('request')->plural($offers->count()) }} sent
            </p>

            <div class="space-y-3">
                @foreach ($offers as $offer)
                    <x-swap.card padding="none" class="space-y-4 p-5 motion-safe:animate-fade-in sm:p-6" wire:key="offer-{{ $offer->id }}">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <x-swap.member :user="$offer->post->user" link :caption="'Sent '.$offer->created_at->diffForHumans()" />
                            <x-swap.status :status="$offer->status" />
                        </div>

                        <a href="{{ route('posts.show', $offer->post) }}" wire:navigate class="group block rounded-full focus-visible:outline-offset-4">
                            <x-swap.trade :offering="$offer->post->offering_skill" :looking="$offer->post->looking_skill"
                                :active="$offer->status !== \App\PostOfferStatus::REJECTED" />
                        </a>

                        @if ($offer->message)
                            <blockquote class="rounded-2xl border border-line bg-raised px-4 py-3 text-sm leading-6 whitespace-pre-line text-fg">
                                <span class="sr-only">Your message:</span>{{ $offer->message }}
                            </blockquote>
                        @endif

                        @can('cancel', $offer)
                            <div class="flex justify-end border-t border-line pt-4">
                                <form method="POST" action="{{ route('posts.offers.cancel', $offer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-swap.button type="submit" variant="ghost" size="sm">
                                        <flux:icon.x-mark variant="micro" />
                                        Cancel request
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
