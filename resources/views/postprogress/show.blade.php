<?php
$otherUser = $offer->user_id === auth()->id() ? $offer->post->user : $offer->user;
$otherUsername = $otherUser->username;
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
                    <x-swap.badge><x-swap.category-icon :name="$offer->post->category->name" class="opacity-80" /> {{ $offer->post->category->name }}</x-swap.badge>
                    <x-swap.status :status="$offer->post->status" />
                </div>

                <x-swap.trade :offering="$offer->post->offering_skill" :looking="$offer->post->looking_skill"
                    :active="$offer->post->status === \App\PostStatus::IN_PROGRESS" stacked :heading-level="1" />

                <div class="space-y-2 border-t border-line pt-5">
                    <p class="eyebrow">Swapping with</p>
                    <x-swap.member :user="$otherUser" link />
                </div>

                <div class="border-t border-line pt-5">
                    @if ($offer->post->status === \App\PostStatus::COMPLETED)
                        <flux:callout icon="check-circle" variant="success"
                            heading="You both confirmed. This exchange is complete." />

                        @if ($myReview)
                            <div class="mt-4 flex items-center justify-between gap-2">
                                <p class="text-sm text-muted">You rated <a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a></p>
                                <x-star-rating :rating="$myReview->review" />
                            </div>
                        @else
                            <form method="POST" action="{{ route('posts.progress.review', $offer) }}" class="mt-5 space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <p class="text-sm font-semibold text-strong">Rate <a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a></p>
                                    <p class="mt-0.5 text-sm text-muted">Your rating helps others decide who to trust.</p>
                                </div>

                                <x-star-rating-input name="rating" />

                                <x-swap.button type="submit" class="w-full">Submit review</x-swap.button>
                            </form>
                        @endif
                    @elseif ($offer->hasBeenCompletedBy(auth()->user()))
                        <div class="space-y-3">
                            <p class="flex items-start gap-2 text-sm leading-6 text-muted">
                                <flux:icon.clock variant="micro" class="mt-1 size-4 shrink-0" aria-hidden="true" />
                                <span>Waiting for <a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a> to confirm</span>
                            </p>

                            <x-swap.button disabled class="w-full">Mark as complete</x-swap.button>
                        </div>
                    @else
                        <form method="POST" action="{{ route('posts.progress.complete', $offer) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            @if ($offer->hasBeenCompletedByOther(auth()->user()))
                                <p class="text-sm text-muted"><a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a> marked this as complete. Confirm to
                                    close it.</p>
                            @endif

                            <x-swap.button type="submit" variant="swap" class="w-full">
                                <flux:icon.check variant="micro" />
                                Mark as complete
                            </x-swap.button>
                        </form>
                    @endif

                    @if ($offer->post->status === \App\PostStatus::IN_PROGRESS)
                        @if ($offer->hasRequestedCancelBy(auth()->user()))
                            <div class="mt-3 space-y-3">
                                <p class="flex items-start gap-2 text-sm leading-6 text-muted">
                                    <flux:icon.clock variant="micro" class="mt-1 size-4 shrink-0" aria-hidden="true" />
                                    <span>Waiting for <a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a> to agree to cancel</span>
                                </p>

                                <x-swap.button variant="danger" disabled class="w-full">Cancel exchange</x-swap.button>
                            </div>
                        @else
                            <form method="POST" action="{{ route('posts.progress.cancel', $offer) }}" class="mt-3 space-y-3"
                                onsubmit="return confirm('Cancel this exchange? It is called off once you both agree.')">
                                @csrf
                                @method('PATCH')

                                @if ($offer->hasRequestedCancelByOther(auth()->user()))
                                    <p class="text-sm text-muted"><a href="{{ route('profile.show', $otherUser) }}" wire:navigate class="font-medium text-strong underline decoration-line-strong underline-offset-2 transition-colors hover:decoration-brand">{{ $otherUsername }}</a> wants to cancel this exchange. Agree to
                                        call it off.</p>
                                @endif

                                <x-swap.button type="submit" variant="danger" class="w-full">Cancel exchange</x-swap.button>
                            </form>
                        @endif
                    @endif
                </div>
            </x-swap.card>

            <x-swap.card padding="none" class="flex h-[36rem] flex-col overflow-hidden">
                <livewire:posts.progress-chat :offer="$offer" />
            </x-swap.card>
        </div>
    </div>
</x-layouts::app>
