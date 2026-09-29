<x-layouts::app title="Home">
    <section id="listings" class="mx-auto w-full max-w-[1200px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        @if (session('status'))
            <flux:callout class="mb-6" icon="information-circle" heading="{{ session('status') }}" />
        @endif

        @if ($awaitingReview->isNotEmpty())
            <flux:callout class="mb-8" icon="star"
                heading="{{ $awaitingReview->count() === 1 ? 'You have an exchange to review' : 'You have ' . $awaitingReview->count() . ' exchanges to review' }}">
                <x-slot name="actions">
                    <x-swap.button size="sm"
                        href="{{ $awaitingReview->count() === 1 ? route('posts.progress.show', $awaitingReview->first()) : route('posts.progress') }}">
                        Review now
                    </x-swap.button>
                </x-slot>
            </flux:callout>
        @endif

        <x-swap.page-header title="Browse skills" class="mb-8">
            Find someone who can teach what you want to learn.
        </x-swap.page-header>

        <livewire:posts.search-and-filter />
    </section>
</x-layouts::app>
