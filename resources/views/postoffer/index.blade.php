<x-layouts::app title="Offers">
    <div class="mx-auto w-full max-w-3xl p-6 lg:p-8">
        <flux:heading size="xl" level="1">Offers</flux:heading>
        <flux:text class="mt-2">Offers people have sent for your posts.</flux:text>

        @if ($offers->isEmpty())
            <flux:callout class="mt-8" icon="inbox" heading="No offers yet." />
        @else
            <div class="mt-8 space-y-4">
                @foreach ($offers as $offer)
                    <flux:card class="space-y-3" wire:key="offer-{{ $offer->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <flux:heading size="lg">{{ $offer->user->username }}</flux:heading>
                                <flux:text size="sm" class="text-zinc-500">
                                    for
                                    <a href="{{ route('posts.show', $offer->post) }}"
                                        class="underline">{{ $offer->post->offering_skill }}</a>
                                    &middot; {{ $offer->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <flux:badge size="sm" :color="$offer->status->color()">{{ $offer->status->label() }}
                            </flux:badge>

                        </div>

                        @if ($offer->message)
                            <flux:text class="whitespace-pre-line">{{ $offer->message }}</flux:text>
                        @endif
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
