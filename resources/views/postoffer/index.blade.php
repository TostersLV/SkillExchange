<x-layouts::app title="Offers">
    <div class="mx-auto w-full max-w-4xl px-4 py-10 sm:px-6 lg:py-14">
        <x-swap.page-header title="Offers">
            Swaps other members have proposed for your posts.
        </x-swap.page-header>

        @if ($offers->isEmpty())
            <x-swap.empty class="mt-8" icon="inbox" title="No offers yet">
                When someone proposes a swap for one of your posts, it will appear here for you to accept or decline.
                <x-slot name="actions">
                    <x-swap.button href="{{ route('posts.create') }}" variant="swap" size="sm">Post an exchange</x-swap.button>
                </x-slot>
            </x-swap.empty>
        @else
            <p class="mt-8 mb-3 text-sm text-muted">
                <strong class="font-bold text-strong">{{ $offers->count() }}</strong> {{ str('offer')->plural($offers->count()) }} received
            </p>

            <div class="space-y-3">
                @foreach ($offers as $offer)
                    @php($isPending = $offer->status === \App\PostOfferStatus::PENDING)
                    <x-swap.card padding="none" wire:key="offer-{{ $offer->id }}"
                        :class="'space-y-4 p-5 motion-safe:animate-fade-in sm:p-6'.($isPending ? ' border-l-4 !border-l-swap sm:pl-[21px]' : '')">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <x-swap.member :user="$offer->user" link />
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-muted">{{ $offer->created_at->diffForHumans() }}</span>
                                <x-swap.status :status="$offer->status" />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <p class="text-xs font-semibold text-muted">For your post</p>
                            <a href="{{ route('posts.show', $offer->post) }}" wire:navigate class="group block rounded-full focus-visible:outline-offset-4">
                                <x-swap.trade :offering="$offer->post->offering_skill" :looking="$offer->post->looking_skill"
                                    :active="$offer->status !== \App\PostOfferStatus::REJECTED" />
                            </a>
                        </div>

                        @if ($offer->message)
                            <blockquote class="rounded-2xl border border-line bg-raised px-4 py-3 text-sm leading-6 whitespace-pre-line text-fg">
                                <span class="sr-only">{{ $offer->user->username }} wrote:</span>{{ $offer->message }}
                            </blockquote>
                        @endif

                        @if (auth()->user()->can('accept', $offer) || auth()->user()->can('reject', $offer))
                            <div class="flex flex-wrap justify-end gap-2 border-t border-line pt-4">
                                @can('reject', $offer)
                                    <form method="POST" action="{{ route('posts.offers.reject', $offer) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-swap.button type="submit" size="sm" variant="danger">Decline</x-swap.button>
                                    </form>
                                @endcan
                                @can('accept', $offer)
                                    <form method="POST" action="{{ route('post.offers.accept', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-swap.button type="submit" size="sm" variant="swap">
                                            <flux:icon.check variant="micro" />
                                            Accept swap
                                        </x-swap.button>
                                    </form>
                                @endcan
                            </div>
                        @endif
                    </x-swap.card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
