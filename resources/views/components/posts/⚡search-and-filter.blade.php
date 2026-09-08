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
        $posts = Post::query()
            ->with(['user', 'category'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('offering_skill', 'like', '%'.$this->search.'%')
                        ->orWhere('looking_skill', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId))
            ->latest()
            ->get();

        return [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
        ];
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" :label="__('Search')" :placeholder="__('Offering, looking for or description...')" class="sm:flex-1" />

        <flux:select wire:model.live="categoryId" :label="__('Category')" class="sm:w-56">
            <flux:select.option value="">{{ __('All categories') }}</flux:select.option>
            @foreach ($categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($search !== '' || $categoryId !== '')
            <flux:button wire:click="clearFilters" variant="ghost" icon="x-mark">{{ __('Clear') }}</flux:button>
        @endif
    </div>

    <x-posts.results :posts="$posts" :filtered="$search !== '' || $categoryId !== ''" />
</div>
