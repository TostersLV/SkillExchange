<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $categoryId = '';

    
    public function clearFilters(): void
    {
        $this->reset('search', 'categoryId');
    }

    public function with(): array
    {
        $posts = Post::query()->with(['user', 'category'])->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('offering_skill', 'like', '%'.$this->search.'%')->orWhere('looking_skill', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId))->latest()->get();

        return [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
        ];
    }
};
?>

<div class="space-y-5">
    <div class="space-y-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-white/10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" :label="__('Search')" :placeholder="__('Offering, looking for or description...')" class="sm:flex-1" />

            <flux:select wire:model.live="categoryId" :label="__('Category')" class="sm:w-56">
                <flux:select.option value="">{{ __('All categories') }}</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-zinc-500 dark:text-zinc-400" wire:loading.remove>
                {{ $posts->count() }} {{ $posts->count() === 1 ? __('post') : __('posts') }}
                @if ($search !== '')
                    {{ __('for') }} <span class="font-medium text-zinc-700 dark:text-zinc-300">&ldquo;{{ $search }}&rdquo;</span>
                @endif
            </p>

            <p class="text-sm text-zinc-500 dark:text-zinc-400" wire:loading>{{ __('Searching..') }}</p>

            @if ($search !== '' || $categoryId !== '')
                <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="x-mark">{{ __('Clear filters') }}</flux:button>
            @endif
        </div>
    </div>

    <div wire:loading.class="opacity-50" class="transition-opacity">
    @if ($posts->isEmpty())
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
            {{ $search !== '' || $categoryId !== ''
                ? __('No posts match your search.')
                : __('No posts yet. Be the first to create one.') }}
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('posts.show', $post) }}" wire:key="post-{{ $post->id }}" wire:navigate class="block">
                    <div class="h-full space-y-3 rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-zinc-300 hover:shadow-sm dark:border-white/10 dark:bg-white/10 dark:hover:border-white/20">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">{{ $post->category->name }}</span>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $post->status === 'available' ? 'bg-green-100 text-green-700 dark:bg-green-400/10 dark:text-green-400' : 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300' }}">
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

                        <hr class="border-zinc-200 dark:border-white/10">

                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $post->user->username }} &middot; {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
    </div>
</div>
