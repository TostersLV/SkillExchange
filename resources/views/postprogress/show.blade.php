<?php
$otherUsername = $offer->user_id === auth()->id() ? $offer->post->user->username : $offer->user->username;
?>
<x-layouts::app :title="$offer->post->offering_skill">
    <div class="mx-auto w-full max-w-5xl p-6 lg:p-8">
        <flux:button href="{{ route('posts.progress') }}" variant="ghost" size="sm" icon="arrow-left">Back to In
            Progress</flux:button>

        <div class="mt-6 grid gap-6 lg:grid-cols-[340px_1fr]">
            <flux:card class="h-fit space-y-6">
                <div class="flex items-center justify-between">
                    <flux:badge size="sm" color="zinc">{{ $offer->post->category->name }}</flux:badge>
                    <flux:badge size="sm"
                        :color="match ($offer->post->status) {\App\PostStatus::COMPLETED => 'green', \App\PostStatus::IN_PROGRESS => 'amber', default => 'zinc',}">
                        {{ $offer->post->status->label() }}
                    </flux:badge>
                </div>

                <div class="space-y-4">
                    <div>
                        <flux:text size="sm" class="text-zinc-500">Offering</flux:text>
                        <flux:heading size="lg">{{ $offer->post->offering_skill }}</flux:heading>
                    </div>

                    <div>
                        <flux:text size="sm" class="text-zinc-500">Looking for</flux:text>
                        <flux:heading size="lg">{{ $offer->post->looking_skill }}</flux:heading>
                    </div>
                </div>

                @if ($offer->post->description)
                    <div>
                        <flux:text size="sm" class="text-zinc-500">Description</flux:text>
                        <flux:text class="mt-1 whitespace-pre-line">{{ $offer->post->description }}</flux:text>
                    </div>
                @endif

                <flux:separator variant="subtle" />

                <flux:text size="sm" class="text-zinc-500">
                    Posted by {{ $offer->post->user->username }} &middot;
                    {{ $offer->post->created_at->diffForHumans() }}
                </flux:text>

                <flux:separator variant="subtle" />

                @if ($offer->post->status === \App\PostStatus::COMPLETED)
                    <flux:callout icon="check-circle" variant="success"
                        heading="You both confirmed. This exchange is complete." />

                    @if ($myReview)
                        <div class="flex items-center gap-2">
                            <flux:text size="sm">You rated {{ $otherUsername }}</flux:text>
                            <x-star-rating :rating="$myReview->review" />
                        </div>
                    @else
                        <form method="POST" action="{{ route('posts.progress.review', $offer) }}" class="space-y-2">
                            @csrf
                            @method('PATCH')

                            <flux:text size="sm">Rate {{ $otherUsername }}</flux:text>

                            <x-star-rating-input name="rating" />

                            <flux:button type="submit" variant="primary" class="w-full">Submit review</flux:button>
                        </form>
                    @endif
                @elseif ($offer->hasBeenCompletedBy(auth()->user()))
                    <div class="space-y-2">
                        <flux:text size="sm">Waiting for {{ $otherUsername }} to confirm</flux:text>

                        <flux:button disabled class="w-full">Mark as complete</flux:button>
                    </div>
                @else
                    <form method="POST" action="{{ route('posts.progress.complete', $offer) }}" class="space-y-2">
                        @csrf
                        @method('PATCH')

                        @if ($offer->hasBeenCompletedByOther(auth()->user()))
                            <flux:text size="sm">{{ $otherUsername }} marked this as complete. Confirm to close
                                it.
                            </flux:text>
                        @endif

                        <flux:button type="submit" variant="primary" class="w-full">Mark as complete</flux:button>
                    </form>
                @endif
            </flux:card>

            <flux:card class="flex h-[32rem] flex-col">
                <flux:heading size="lg">Chat with {{ $otherUsername }}</flux:heading>

                <div
                    class="mt-4 flex-1 overflow-y-auto rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <flux:text size="sm" class="text-zinc-500">No messages yet.</flux:text>
                </div>

                <div class="mt-4 flex items-end gap-2">
                    <flux:input rows="1" placeholder="Write your message here" disabled class="flex-1" />
                    <flux:button variant="primary" disabled>Send</flux:button>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
