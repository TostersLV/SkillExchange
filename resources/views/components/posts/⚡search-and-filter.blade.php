<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
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
                    $query
                        ->where('offering_skill', 'like', '%' . $this->search . '%')
                        ->orWhere('looking_skill', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryId !== '', fn($query) => $query->where('category_id', $this->categoryId))
            ->latest()
            ->get();

        return [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
        ];
    }
};
?>

<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="sm:flex-1">
            <x-swap.input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" label="Search"
                placeholder="Offering, looking for or description..." />
        </div>

        @if ($search !== '' || $categoryId !== '')
            <x-swap.button wire:click="clearFilters" variant="ghost" class="sm:mb-px">
                <flux:icon.x-mark variant="micro" />
                Clear
            </x-swap.button>
        @endif
    </div>

    <div class="flex flex-wrap gap-2" role="group" aria-label="Filter by category">
        <x-swap.chip wire:click="$set('categoryId', '')" :active="$categoryId === ''">All</x-swap.chip>

        @foreach ($categories as $category)
            <x-swap.chip wire:click="$set('categoryId', '{{ $category->id }}')"
                :active="(string) $categoryId === (string) $category->id">
                {{ $category->name }}
            </x-swap.chip>
        @endforeach
    </div>

    <div class="pt-4">
        <x-posts.results :posts="$posts" :filtered="$search !== '' || $categoryId !== ''" />
    </div>
</div>
