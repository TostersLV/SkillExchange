<x-layouts::app :title="__('Requests')">
    <div class="mx-auto w-full max-w-3xl p-6 lg:p-8">
        <flux:heading size="xl" level="1">{{ __('Requests') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Offers you have sent to other people.') }}</flux:text>

        @if ($offers->isEmpty())
            <flux:callout class="mt-8" icon="paper-airplane" :heading="__('You have not sent any offers yet.')" />
        @else
            <div class="mt-8 space-y-4">
                @foreach ($offers as $offer)
                    <flux:card class="space-y-3" wire:key="offer-{{ $offer->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <flux:text size="sm" class="text-zinc-500">
                                    {{ __('To') }} {{ $offer->post->user->username }} &middot; {{ $offer->created_at->diffForHumans() }}
                                </flux:text>
                                <a href="{{ route('posts.show', $offer->post) }}" class="underline">
                                    <flux:heading size="lg">{{ $offer->post->offering_skill }}</flux:heading>
                                </a>
                            </div>
                            <flux:badge size="sm" :color="$offer->status->color()">{{ $offer->status->label() }}</flux:badge>
                        </div>

                        @if ($offer->message)
                            <flux:text class="whitespace-pre-line">{{ $offer->message }}</flux:text>
                        @endif

                        @can('cancel', $offer)
                            <div class="flex justify-end">
                                <form method="POST" action="{{ route('posts.offers.cancel', $offer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="ghost" size="sm" icon="x-mark">{{ __('Cancel offer') }}</flux:button>
                                </form>
                            </div>
                        @endcan
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
