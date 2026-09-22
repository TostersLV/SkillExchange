<x-layouts::app title="Home">
    <div class="mx-auto w-full max-w-5xl p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Skill exchange posts</flux:heading>
                <flux:text class="mt-2">Browse skills people are offering and what they want in return.</flux:text>
            </div>
        </div>

        @if (session('status'))
            <flux:callout class="mt-6" icon="information-circle" heading="{{ session('status') }}" />
        @endif

        @if ($awaitingReview->isNotEmpty())
            <flux:callout class="mt-6" icon="star"
                heading="{{ $awaitingReview->count() === 1 ? 'You have an exchange to review' : 'You have '.$awaitingReview->count().' exchanges to review' }}">
                <x-slot name="actions">
                    <flux:button size="sm"
                        href="{{ $awaitingReview->count() === 1 ? route('posts.progress.show', $awaitingReview->first()) : route('posts.progress') }}">
                        Review now
                    </flux:button>
                </x-slot>
            </flux:callout>
        @endif

        <div class="mt-8">
            <livewire:posts.search-and-filter />
        </div>
    </div>
</x-layouts::app>
