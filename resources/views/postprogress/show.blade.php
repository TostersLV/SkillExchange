<?php
$otherUsername = $offer->user_id === auth()->id() ? $offer->post->user->username : $offer->user->username;
?>
<x-layouts::app :title="$offer->post->offering_skill">
    <div class="mx-auto w-full max-w-[1200px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <x-swap.button href="{{ route('posts.progress') }}" variant="ghost" size="sm" class="-ms-3">
            <flux:icon.arrow-left variant="micro" />
            Back to In progress
        </x-swap.button>

        <div class="mt-6 grid gap-6 lg:grid-cols-[380px_1fr]">
            <x-swap.card class="h-fit space-y-6">
                <div class="flex items-center justify-between gap-2">
                    <x-swap.badge>{{ $offer->post->category->name }}</x-swap.badge>
                    <x-swap.status :status="$offer->post->status" />
                </div>

                <div class="space-y-3">
                    <div class="rounded-lg border border-line bg-raised p-4">
                        <p class="eyebrow">Offering</p>
                        <h1 class="mt-1 text-lg leading-snug font-semibold break-words text-strong">
                            {{ $offer->post->offering_skill }}</h1>
                    </div>

                    <div class="flex justify-center text-strong">
                        <span class="flex size-7 items-center justify-center rounded-full border border-line-strong bg-surface">
                            <x-swap.icon class="size-3.5 rotate-90" />
                            <span class="sr-only">in exchange for</span>
                        </span>
                    </div>

                    <div class="rounded-lg border border-line bg-raised p-4">
                        <p class="eyebrow">Looking for</p>
                        <p class="mt-1 text-lg leading-snug font-semibold break-words text-strong">
                            {{ $offer->post->looking_skill }}</p>
                    </div>
                </div>

                @if ($offer->post->description)
                    <div>
                        <p class="eyebrow">Description</p>
                        <p class="mt-1.5 text-sm leading-6 whitespace-pre-line text-fg">{{ $offer->post->description }}</p>
                    </div>
                @endif

                <p class="border-t border-line pt-5 text-sm text-muted">
                    Posted by <span class="font-medium text-fg">{{ $offer->post->user->username }}</span> &middot;
                    {{ $offer->post->created_at->diffForHumans() }}
                </p>

                <div class="border-t border-line pt-5">
                    @if ($offer->post->status === \App\PostStatus::COMPLETED)
                        <flux:callout icon="check-circle" variant="success"
                            heading="You both confirmed. This exchange is complete." />

                        @if ($myReview)
                            <div class="mt-4 flex items-center justify-between gap-2">
                                <p class="text-sm text-muted">You rated {{ $otherUsername }}</p>
                                <x-star-rating :rating="$myReview->review" />
                            </div>
                        @else
                            <form method="POST" action="{{ route('posts.progress.review', $offer) }}" class="mt-5 space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <p class="text-sm font-semibold text-strong">Rate {{ $otherUsername }}</p>
                                    <p class="mt-0.5 text-sm text-muted">Your rating helps others decide who to trust.</p>
                                </div>

                                <x-star-rating-input name="rating" />

                                <x-swap.button type="submit" class="w-full">Submit review</x-swap.button>
                            </form>
                        @endif
                    @elseif ($offer->hasBeenCompletedBy(auth()->user()))
                        <div class="space-y-3">
                            <p class="flex items-center gap-2 text-sm text-muted">
                                <flux:icon.clock variant="micro" class="size-4" aria-hidden="true" />
                                Waiting for {{ $otherUsername }} to confirm
                            </p>

                            <x-swap.button disabled class="w-full">Mark as complete</x-swap.button>
                        </div>
                    @else
                        <form method="POST" action="{{ route('posts.progress.complete', $offer) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            @if ($offer->hasBeenCompletedByOther(auth()->user()))
                                <p class="text-sm text-muted">{{ $otherUsername }} marked this as complete. Confirm to
                                    close it.</p>
                            @endif

                            <x-swap.button type="submit" class="w-full">
                                <flux:icon.check variant="micro" />
                                Mark as complete
                            </x-swap.button>
                        </form>
                    @endif
                </div>
            </x-swap.card>

            <x-swap.card class="flex h-[34rem] flex-col">
                <livewire:posts.progress-chat :offer="$offer" />
            </x-swap.card>
        </div>
    </div>
</x-layouts::app>
