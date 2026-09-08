@props(['posts', 'filtered' => false])

@if ($posts->isEmpty())
    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $filtered ? __('No posts match your search.') : __('No posts yet. Be the first to create one.') }}</div>
@else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post) }}" wire:key="post-{{ $post->id }}" class="block">
                <div class="h-full space-y-3 rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $post->category->name }}</span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $post->status === 'available' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ ucfirst($post->status) }}</span>
                    </div>

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Offering') }}</p>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $post->offering_skill }}</h3>
                    </div>

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Looking for') }}</p>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $post->looking_skill }}</h3>
                    </div>

                    @if ($post->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $post->description }}</p>
                    @endif

                    <hr class="border-zinc-200 dark:border-zinc-700">

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $post->user->username }} &middot; {{ $post->created_at->diffForHumans() }}</p>
                </div>
            </a>
        @endforeach
    </div>
@endif
